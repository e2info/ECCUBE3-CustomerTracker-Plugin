<?php

namespace Plugin\CustomerTracker\Controller\Admin;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class TrackerController
{

    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        if ($app['request']->getMethod() === 'POST') {
            return $this->ajax($app, $request);
        }
        $fullscreen = $app['request']->get('fullscreen');
        $sessTimeout = ini_get('session.gc_maxlifetime');
        $activeCount = $app['eccube.plugin.customer_tracker.repository.history']->getActiveCount($sessTimeout);
        $timeRange = $sessTimeout;
        $range = $app['request']->get('range');
        $rangeText = "有効セッション";
        if ($range == 'day') {
            $timeRange = 24 * 3600;
            $rangeText = "本日";
        } elseif ($range == 'week') {
            $timeRange = 24 * 7 * 3600;
            $rangeText = "一週間";
        } elseif ($range == 'month') {
            $timeRange = 24 * 30 * 3600;
            $rangeText = "30日間";
        }
        
        $updateInterval = (int) $app['request']->get('interval');
        if ($updateInterval <= 0) {
            $updateInterval = 3;
        }
        
        $vars = array(
            'sessTimeout' => $sessTimeout,
            'activeCount' => $activeCount,
            'rangeText' => $rangeText,
            'updateInterval' => $updateInterval,
            'timeRange' => $timeRange
        );
        
        $type = $app['request']->get('type');
        if ($type == 'force') {
            $typeText = "URIチャート";
            $template = 'CustomerTracker/View/Admin/force.twig';
            $groupedHistory = $app['eccube.plugin.customer_tracker.repository.history']->getGroupedHistory($timeRange);
            $latestHistory = null;
            if (sizeof($groupedHistory) > 0) {
                $latestHistory = array_values($groupedHistory)[0][0];
            }
            $relations = $app['eccube.plugin.customer_tracker.repository.history']->getUriRelations($groupedHistory);
            $vars['forceHistoryJson'] = json_encode($relations);
            $vars['latestHistory'] = $latestHistory;
        } else {
            $typeText = "一覧表示";
            $template = 'CustomerTracker/View/Admin/list.twig';
            $groupedHistory = $app['eccube.plugin.customer_tracker.repository.history']->getGroupedHistory($timeRange);
            $latestHistory = null;
            if (sizeof($groupedHistory) > 0) {
                $latestHistory = array_values($groupedHistory)[0][0];
            }
            $vars['groupedHistory'] = $groupedHistory;
            $vars['latestHistory'] = $latestHistory;
        }
        
        $vars['typeText'] = $typeText;
        $vars['template'] = $template;

        if ($fullscreen) {
            return $app['view']->render('CustomerTracker/View/Admin/fullscreen_tracker.twig', $vars);
        } else {
            $str = $app['view']->render('CustomerTracker/View/Admin/tracker.twig', $vars);
            $str = preg_replace('/<h1\s*class\s*\=\s*\"page\-header\">(.*)<\/h1>/is', '${1}', $str);
            return $str;
        }
        
    }

    function ajax(Application $app, Request $request)
    {
        //if (! $request->isXmlHttpRequest()) {
        //    return "";
        //}
        $latestHistoryId = (int) $app['request']->get('latest');
        switch ($app['request']->get('type')) {
        case 'list':
            if ($latestHistoryId > 0) {
                $groups = $app['eccube.plugin.customer_tracker.repository.history']->getLatestHistoryGroups($latestHistoryId);
            } else {
                $timeRange = (int) $app['request']->get('timerange');
                $groups = $app['eccube.plugin.customer_tracker.repository.history']->getGroupedHistory($timeRange);
            }
            // change groups to array
            if ($groups) {
                foreach ($groups as $key => $histories) {
                    $data = array();
                    foreach ($histories as $history) {
                        $data[] = $history->getDataArray();
                    }
                    $groups[$key] = $data;
                }
            }
            return $app->json($groups);
        case 'force':
            if ($latestHistoryId > 0) {
                $groupedHistory = $app['eccube.plugin.customer_tracker.repository.history']->getLatestHistoryGroups($latestHistoryId);
            } else {
                $timeRange = (int) $app['request']->get('timerange');
                $groupedHistory = $app['eccube.plugin.customer_tracker.repository.history']->getGroupedHistory($timeRange);
            }
            if (sizeof($groupedHistory) > 0) {
                $latestHistory = array_values($groupedHistory)[0][0];
            }
            $relations = $app['eccube.plugin.customer_tracker.repository.history']->getUriRelations($groupedHistory);
            if ($latestHistory) {
                $relations['latestHistoryId'] = $latestHistory->getId();
            }
            return $app->json($relations);
        }
        return "";
    }
}
