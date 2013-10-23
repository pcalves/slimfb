<?php
// channel
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

// index
$app->map(
    '/',
    $auth($app, $config),
    function () use ($app, $config) {
        if ($config['general']['env'] == 'local') {
            $app->render('index.twig');
        } else if (!$app->likes) {
            // show prelike page
            $app->render('prelike.twig');
        } else {
            $app->render('index.twig');
        }
    }
)->via('GET', 'POST');
