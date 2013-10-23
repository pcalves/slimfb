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
define('DS', DIRECTORY_SEPARATOR);
// Start session
session_start();
// IE Cookies Fix
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
// Detect environment
// change local server ip
if ($_SERVER['SERVER_ADDR'] == '172.90.90.90') {
    $config = include './config/local.php';
} else {
    $config = include './config/prod.php';
}

// Require app components
require 'vendor/autoload.php';
require 'vendor/facebook/php-sdk/src/facebook.php';

// Initiate slim, configure slim to use twig
// https://github.com/codeguy/Slim-Views#twig
$app = new \Slim\Slim(
    array(
        'templates.path' => './views',
        'view' => new \Slim\Views\Twig
    )
);
$view = $app->view();

// Twig options
// https://github.com/codeguy/Slim-Views#how-to-use-1
$view->parserOptions = array(
    'debug' => true,
    // 'cache' => dirname(__FILE__) . '/cache'
);
// enable helper functions
// urlFor  https://github.com/codeguy/Slim-Views#urlfor
// siteUrl https://github.com/codeguy/Slim-Views#siteurl
// baseUrl https://github.com/codeguy/Slim-Views#baseurl
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

// Load facebook and define all necessary app parameters
$app->facebook = new Facebook(
    array(
        'appId'  => $config['facebook']['appid'],
        'secret' => $config['facebook']['secret'],
        'cookie' => true
    )
);
$app->user = null;
$app->likes = false;

// load Paris models
$models = glob("./models/*.php");
foreach ($models as $filename) {
    include $filename;
}

// Idiorm basic config — DB Connection
ORM::configure($config['database']['dsn']);
ORM::configure('username',       $config['database']['username']);
ORM::configure('password',       $config['database']['password']);
// tell server to expect utf-8 encoded characters
// you can also achieve the same by addind ";charset=utf8" to your dsn
// http://stackoverflow.com/questions/1650591/whether-to-use-set-names
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// Pass data to every request
$app->hook(
    'slim.before.dispatch',
    function () use ($app, $config) {
        $app->view()->setData('config', $config);
    }
);

// FB authentication function
$auth = function ($app, $config) {
    return function () use ($app, $config) {
        // if localhost, bypass authentication
        if ($config['general']['env'] === 'local') {
            return true;
        }

        $fb_scope = $config['facebook']['scope'];
        $canvas_page = $config['facebook']['canvas_page'];
        $user_id = $app->facebook->getUser();
        // if user permissions check fails, request permissions
        if (!$user_id) {
            // load permissions window
            $params = array(
                'scope' => implode(',', $fb_scope),
                'redirect_uri' => $canvas_page
            );
            $auth_url = $app->facebook->getLoginUrl($params);
            echo "<script>parent.location.href='" . $auth_url . "'</script>";
            exit();
        } else {
            // Proceed knowing you have a logged in user who's authenticated.
            try {
                $token = $app->facebook->getAccessToken();
                // set user
                $me = $app->facebook->api('/me');
                // check if user likes page
                $likes = $app->facebook->api("/me/likes/" . $config['facebook']['pageid']);
                if (!empty($likes['data'])) $app->likes = true;
            } catch (FacebookApiException $e) {
                error_log($e);
            }
        }
    };
};

// Load routes
$routes = glob("./routes/*.php");
foreach ($routes as $filename) {
    include $filename;
}

$app->run();
