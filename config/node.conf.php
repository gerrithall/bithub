<?php

/**
 * node.conf.php
 * configurations in this file should be node specific. 
 * configurations that are project specific should go in app.conf.php
 * 
 * copyright 2015 zcor
 */

define('DBSERVER','');
define('DBUSER','');
define('DBPASS','');
define('DBPRIMARY','');

# git shit
define("GITHUB_USERAGENT", "");
define("GITHUB_TOKEN", "");
define("GITHUB_CLIENTID", "");
define("GITHUB_CLIENTSECRET", "");

# bit shit
define("BTC_WALLET_KEY", "");

# coinprism
define("COINPRISM_USERNAME", "");
define("COINPRISM_PASSWORD", "");

# hotwallet key
define("HOTWALLET_KEY", "");
define("HOTWALLET_ASSET", "");
define("HOTWALLET_ADDR", "");


define("ERROR_EMAIL", "");
define('SITE_PATH',"");
define('SMARTY_PATH','');

define('DOMAIN', 'gobithub.com');
define('SITE_URL','http://'.DOMAIN); 

define('SYSTEM_LOG_PATH','');
define('QUERY_LOG_PATH','');

//Set the application to load right here
require_once(SITE_PATH . '/config/platform.conf.php');

?>
