<?php
// Channel URL
// Fixes certain cross-domain issues for certain browsers.
$app->map(
    '/channel',
    $auth($app, $config),
    function () use ($app, $config) {
        $cache_expire = 60*60*24*365;
        header("Pragma: public");
        header("Cache-Control: max-age=".$cache_expire);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
        echo '<script src="//connect.facebook.net/en_US/all.js"></script>';
    }
)->via('GET', 'POST');

// Fix for third party cookies (Safari)
$app->map(
    '/cookies_fix',
    function () use ($app, $config) {
        setcookie('cookie_fix', true);
        header("location: ".$config['facebook']['canvas_page']);
    }
)->via('GET', 'POST');

// Index
$app->map(
    '/',
    $auth($app, $config),
    function () use ($app, $config) {
        if ($config['general']['env'] == 'local') {
            $app->render('pages/index.twig');
        } else if (!$app->likes) {
            // show fangate
            // TODO: remove fangates after November 5th, 2014
            $app->render('pages/fangate.twig');
        } else {
            $app->render('pages/index.twig');
        }
    }
)->via('GET', 'POST');
