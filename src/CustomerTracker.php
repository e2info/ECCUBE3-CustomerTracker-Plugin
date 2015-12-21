<?php

namespace Plugin\CustomerTracker;

use Plugin\CustomerTracker\Entity\History;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerTracker
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function prepareCustomerTracker()
    {
        $app = $this->app;
        $app->on(\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, function (\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event) use($app)
        {
            if (\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.renderafter', $event);
        }, $app::LATE_EVENT);
    }

    public function recordHistory($event)
    {
        $app = $this->app;
        if (strpos($app['request']->getPathInfo(), '/' . trim($app['config']['admin_route'], '/')) === 0) {
            return;
        }
        if ($app['session']) {
            $em = $app['orm.em'];
            $history = new History();
            $history->setSessionId($app['session']->getId());
            $customer = null;
            if ($app['user'] instanceof \Eccube\Entity\Customer) {
                $customer = $app['user'];
                $history->setCustomer($customer);
            }
            $uri = substr($app['request']->getRequestUri(), 0, 255);
            $history->setUri($uri);
            $history->setReferrer($app['request']->headers->get('referer'));
            $visited = new \DateTime();
            $lastHistory = $app['eccube.plugin.customer_tracker.repository.history']->findLastOne($history);
            if ($lastHistory) {
                $lastHistory->setStayed($visited->getTimestamp() - $lastHistory->getVisited()
                    ->getTimestamp());
                $em->persist($lastHistory);
            }
            $history->setVisited($visited);
            $em->persist($history);
            $em->flush();
            
            if (rand(0, 999) == 1) { // execute delete every 1000 access
                $em = $app['orm.em'];
                $qb = $em->createQueryBuilder();
                $qb->delete("Plugin\CustomerTracker\Entity\History", "h")
                    ->Where('h.visited < :date')
                    ->setParameter('date', (new \DateTime())->sub(new \DateInterval('P31D')));
                $qb->getQuery()->execute();
            }
        }
        return;
    }
}
