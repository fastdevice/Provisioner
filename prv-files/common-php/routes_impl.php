<?php

require_once '/var/www/prv-files/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

include '/var/www/prv-files/common-php/validate_impl.php';
include '/var/www/prv-files/common-php/paths_impl.php';


# Path to config files
$path_ctrls = "/var/www/prv-files/accounts-php/";


# Include controllers to enable accounts
include ($path_ctrls . 'master_route.php');
include ($path_ctrls . 'boot_route.php');


/* 
 * Execute controllers, no further code past here
 */
$app->run();
