#!/usr/bin/php5 -q
<?php

define('CRON_MODE',1);

ini_set('max_execution_time',0);
ini_set('memory_limit','768M');

$dir = explode('/',__FILE__);
array_pop($dir);
$dir = implode('/',$dir);

require_once($dir . '/../../config/node.conf.php');
require_once(MODULES_PATH . '/Cron.lib.php');

mb_internal_encoding("UTF-8");

try {
        $cron = new Cron();
        $cron->run_script();
}
catch (Exception $e) {
        Gbl::get('system_log')->write(LOG_LEVEL_ERROR, 'Fatal website error (run-cron.php):' . $e->getMessage());
        die('Exception!');
}


?>
