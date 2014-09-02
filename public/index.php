<?php
/**
 * Facebook Apps
 *
 * @category Application
 * @package  Others/slimfb
 * @author   Paulo Alves <pauloalves@others.pt>
 * @license  The MIT License http://opensource.org/licenses/MIT
 * @link     http://github.com/pcalves/slimbfb
 *
 */

// COMPONENTS
require '../vendor/autoload.php';



// CONSTANTS
// directory separator
define("DS", "/", true);
// base path
define('BASE_PATH', realpath(__DIR__).DS, true);



// Start session
session_start();
// IE Cookies Fix
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');



// ENVIRONMENT AND CONFIG
// TODO: improve environment detection code
if (fnmatch('*.dev', $_SERVER['SERVER_NAME'])) {
    $config = include '../app/config/local.php';
} else {
    $config = include '../app/config/prod.php';
}



// Logging with Monolog
$logger = new \Flynsarmy\SlimMonolog\Log\MonologWriter(
    array(
        'handlers' => array(
            new \Monolog\Handler\StreamHandler('../storage/logs/'.date('Y-m-d').'.log'),
        ),
    )
);



// SLIM & TWIG
// https://github.com/codeguy/Slim-Views#twig
$app = new \Slim\Slim(
    array(
        'templates.path' => '../app/views',
        'view'           => new \Slim\Views\Twig,
        'log.writer'     => $logger

    )
);
$view = $app->view();



// TWIG CONFIG
// https://github.com/codeguy/Slim-Views#how-to-use-1
$view->parserOptions = array(
    'cache' => realpath('../storage/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);

// enable helper functions
// urlFor  https://github.com/codeguy/Slim-Views#urlfor
// siteUrl https://github.com/codeguy/Slim-Views#siteurl
// baseUrl https://github.com/codeguy/Slim-Views#baseurl
$view->parserExtensions = array(new \Slim\Views\TwigExtension());



// FACEBOOK & APP PARAMS
$app->facebook = new Facebook(
    array(
        'appId'  => $config['facebook']['appid'],
        'secret' => $config['facebook']['secret'],
        'cookie' => true
    )
);
$app->user = null;
$app->likes = false;



// PARIS (MODELS)
// $models = glob("../app/models/*.php");
// foreach ($models as $filename) {
//     include $filename;
// }



// IDIORM (ORM)
ORM::configure($config['database']['dsn']);
ORM::configure('username',       $config['database']['username']);
ORM::configure('password',       $config['database']['password']);
// tell server to expect utf-8 encoded characters
// you can also achieve the same by addind ";charset=utf8" to your dsn
// http://stackoverflow.com/questions/1650591/whether-to-use-set-names
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));



// This hook runs before every request
$app->hook(
    'slim.before.dispatch',
    function () use ($app, $config) {
        $app->view()->setData('config', $config);
    }
);



// FACEBOOK AUTHENTICATION
$auth = function ($app, $config) {
    return function () use ($app, $config) {
        // if localhost, bypass authentication
        if ($config['general']['env'] === 'local') {
            return true;
        }

        // fix for third party cookies (Safari)
        if (!isset($_REQUEST['signed_request'])
            && !isset($_COOKIE['cookie_fix'])
        ) {
            exit("<script>window.top.location='"
                . $config['general']['base_url'].DS.'cookies_fix'
                . "'</script>"
            );
        }

        $fb_scope       = $config['facebook']['scope'];
        $canvas_page    = $config['facebook']['canvas_page'];
        $user_id        = $app->facebook->getUser();
        $signed_request = $app->facebook->getSignedRequest();

        // if user permissions check fails, request permissions
        if (!$user_id) {
            // passing parameters to page tab using app_data
            if (isset($signed_request["app_data"])) {
                $canvas_page .= "&app_data=" . $signed_request["app_data"];
            }

            $params = array(
                'scope'        => implode(',', $fb_scope),
                'redirect_uri' => $canvas_page
            );
            $auth_url = $app->facebook->getLoginUrl($params);
            exit("<script>parent.location.href='".$auth_url."'</script>");
        } else {
            // Proceed knowing you have a logged in user who's authenticated.
            // Set access token to 60 days
            $app->facebook->setExtendedAccessToken();
            $token = $app->facebook->getAccessToken();

            // set user
            $me = $app->facebook->api(
                '/me', 'get', array(
                    'access_token' => $token
                )
            );
            // check if user likes page
            if (isset($signed_request["page"])
                && $signed_request["page"]["liked"]
            ) {
                $app->likes = true;
            }
        }
    };
};



// ROUTES
$routes = glob("../app/routes/*.php");
foreach ($routes as $filename) {
    include $filename;
}



// HELPERS
// require '../app/helpers.php';



// RUN APP
$app->run();
