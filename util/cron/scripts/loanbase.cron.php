<?php

$time = 0;
$ts = microtime(true);

require_once(MODULES_PATH . '/Loanbase.lib.php');
$L = new Loanbase;
$L->refresh_data();
echo("A".$x++.' '.(microtime(true) - $ts)."<br/>");

?>
