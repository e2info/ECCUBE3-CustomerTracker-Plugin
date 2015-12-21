<?php

namespace Plugin\CustomerTracker\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CustomerTrackerServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        $app->match('/' . $app["config"]["admin_route"] . '/customer/tracker', '\Plugin\CustomerTracker\Controller\Admin\TrackerController::index')->bind('plugin_CustomerTracker_admin_tracker');
        
        // Repository
        $app['eccube.plugin.customer_tracker.repository.history'] = $app->share(function () use($app)
        {
            return $app['orm.em']->getRepository('Plugin\CustomerTracker\Entity\History');
        });
        // Service
        $app['eccube.plugin.customer_tracker.service.tracker'] = $app->share(function () use($app)
        {
            return new \Plugin\CustomerTracker\Service\CustomerTrackerService($app);
        });
        
        $app['config'] = $app->share($app->extend('config', function ($config)
        {
            $config['nav'][2]['child'][] = array(
                'id' => 'plugin_CustomerTracker_admin_tracker',
                'name' => 'トラッカー',
                'url' => 'plugin_CustomerTracker_admin_tracker'
            );
            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
