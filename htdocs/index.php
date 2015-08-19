<?php
/*
 * index.php
 *
 * All traffic goes through here.
 *
 * copyright 2015 zcor
 *
 */

if(!file_exists("../config/node.conf.php")) {
	die(file_get_contents("install.html"));
}
require_once('../config/node.conf.php');

mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
ob_start("mb_output_handler");

try {
	$scout = new Scout();
	$controller = $scout->get_controller();
	$controller->run();
}
catch (Exception $e) {
	Gbl::get('system_log')->write(LOG_LEVEL_ERROR, 'Fatal website error (index.php):' . $e->getMessage());
	if(MODE == 'staging') {
		die("LOGJAMMIN! ".$e->getMessage());
	} else {
		die('Exception Logged!');
	}
}

ob_end_flush();

?>
