<?php

return array(
    'general'  => array(
        'env'         => 'prod',
        'base_url'    => 'http://example.com'
    ),

    'metadata' => array(
        'title'       => 'Application title',
        'description' => 'Application description'
    ),

    'facebook' => array(
        'appid'       => 'XXXXXXXXXXXXX',
        'secret'      => 'XXXXXXXXXXXXX',
        'scope'       => array('email'),
        'canvas_page' => 'https://www.facebook.com/pages/PageName/PAGEID?sk=app_APPID'
    ),

    'database' => array(
        'dsn'         => 'mysql:host=localhost;dbname=DBNAME',
        'username'    => 'root',
        'password'    => 'root'
    ),

    // any application rules (timestamps, number limits, etc.) go here!
    'rules'    => array(
    )
);
