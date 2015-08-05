<?php

require_once(MODULES_PATH . '/Bitcoin.lib.php');

$BTC = new Bitcoin;
$BTC->sniff_wallets();

?>
