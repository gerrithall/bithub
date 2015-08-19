<?php
/**
 * platform.conf.php
 * configurations in this file should be platform specific.
 * configurations that are node specific should go in node.conf.php
 *
 * copyright 2015 zcor
 */

session_start();
define('SITE_AVAILABLE',1);

define('MODE', 'staging');
define("SITE_TYPE", 'bithub');

if(MODE == 'staging') {
	ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING);
} else {
	ini_set('error_reporting', 0);
}

define('MODULES_PATH',SITE_PATH . 'lib/modules');
define('TEMPLATE_URL',SITE_URL . '/template');
define('TEMPLATE_PATH',SITE_PATH . 'template');
define('CONTROLLERS_PATH',SITE_PATH . 'lib/controllers');
define('CRON_PATH',SITE_PATH . 'util/cron/scripts');
define('DBACL_PATH',SITE_PATH . 'util/dbacl');
define('FONT_PATH',SITE_PATH . 'util/font/');
define('CONFIG_PATH',SITE_PATH . 'config');
define('TMP_PATH',SITE_PATH . 'util/tmp/');
define('SMARTY_CLASS_PATH',SMARTY_PATH);
define('SMARTY_TEMPLATE_C_PATH',sys_get_temp_dir());
define('SMARTY_CONFIGS_PATH',SITE_PATH . 'util/smarty/configs');
define('SMARTY_CACHE_PATH',SITE_PATH . 'util/smarty/cache');

define('EMAIL_REGEX','/(([a-z0-9!#$%&*+-=?^_`{|}~][a-z0-9!#$%&*+-=?^_`{|}~.]*[a-z0-9!#$%&*+-=?^_`{|}~])|[a-z0-9!#$%&*+-?^_`{|}~]|("[^"]+"))[@]([-a-z0-9]+\.)+([a-z]{2}|com|net|edu|org|gov|mil|int|biz|pro|info|arpa|aero|coop|name|museum)/ixu');
define('PASSWORD_REGEX','/^[\S]{4,16}$/u');
define('SCREEN_NAME_REGEX','/^[A-Za-z0-9 ]{2,16}$/u');
define('SCREEN_NAME_SEARCH_REGEX','/^[A-Za-z0-9 ]{1,16}$/u');
define('SCREEN_NAME_EXTRA_REGEX','/^[\'A-Za-z0-9 ]{2,64}$/u');
define('ZIP_CODE_REGEX','/^[A-Za-z0-9 ]{3,8}$/u');
define('MD5_REGEX','/^[0-9a-f]{32}$/');
define('SIGNUP_CODE_REGEX','/^[A-Za-z0-9]{5}$/u');

# log levels -- used to control Log sensitivity
define('LOG_LEVEL_ALL',4);
define('LOG_LEVEL_NOTICE',3);
define('LOG_LEVEL_WARNING',2);
define('LOG_LEVEL_ERROR',1);
define('LOG_LEVEL_NONE',0);

# Modules
require_once(MODULES_PATH . '/Gbl.lib.php');
require_once(MODULES_PATH . "/Database.php");
require_once(MODULES_PATH . '/Log.lib.php');
require_once(MODULES_PATH . '/Cleaner.lib.php');
require_once(MODULES_PATH . '/Scout.lib.php');
#require_once(MODULES_PATH . '/HTTP.lib.php');
#require_once(MODULES_PATH . '/Cookie.lib.php');
#require_once(MODULES_PATH . '/CookieStore.lib.php');
#require_once(MODULES_PATH . '/JSON.lib.php');

# Abstract Controller
require_once(CONTROLLERS_PATH . '/Controller.lib.php');

require_once(SMARTY_CLASS_PATH . '/SmartyBC.class.php');
$smarty = new SmartyBC();
$smarty->template_dir = TEMPLATE_PATH;
$smarty->compile_dir = SMARTY_TEMPLATE_C_PATH;
$smarty->config_dir = SMARTY_CONFIGS_PATH;
$smarty->cache_dir = SMARTY_CACHE_PATH;
$smarty->assign('site_url',SITE_URL);
$smarty->assign('template_url',TEMPLATE_URL);

if(MODE == 'staging') {
	$smarty->caching = 0;
	$smarty->cache_lifetime = 0;
}

Gbl::store('template_engine',$smarty);

# create the system log, used for most logging
$system_log = new Log(SYSTEM_LOG_PATH,LOG_LEVEL_ALL);
Gbl::store('system_log',$system_log);

# authentication types -- auth module
define('AUTH_SITE',0);

# cleaner types -- used by the Cleaner module.
define('CTYPE_ARRAY',0);
define('CTYPE_REGEX',1);
define('CTYPE_INT',2);
define('CTYPE_ID',3);
define('CTYPE_POS_NUMERIC',4);
define('CTYPE_BOOL',5);
define('CTYPE_POS_INT',6);

# auth params
define('AUTH_COOKIE','auth');
define('AUTH_COOKIE_EXPIRE',24*60*60);
define('AUTH_COOKIE_DOMAIN', DOMAIN);

define('DEFAULT_MINER_FEE', 1000);
define('DEFAULT_DIVISIBILITY', 6);

# Controllers
require_once(CONFIG_PATH . '/pages.conf.php');
?>
