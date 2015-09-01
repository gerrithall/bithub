<?php
	require_once(MODULES_PATH.'/Email.lib.php');

	$Email = new Email;
	$Email->send_scheduled_emails();

?>
