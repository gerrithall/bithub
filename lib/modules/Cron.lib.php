<?php
/*
 * Cron.lib.php
 * Run scripts on the command line using all of the site tools.
 * 
 * copyright 2008 gerrit hall
 */
class Cron {
	protected static $current;
	private $_script_name;
	private $_cron_name;
	private $_generator;
	public $arguments;

	public static function get_current() {
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class Cron.');
		}
		$this->_parse_arguments();
		self::$current = $this;
	}

	private function _parse_arguments() {
		$this->_script_name = array_shift($_SERVER['argv']);
		$this->_cron_name = array_shift($_SERVER['argv']);
		$this->arguments = $_SERVER['argv'];
	}

	public function run_script() {
		$t = microtime(true);
		$start_mysqltime = strftime("%Y-%m-%d %H:%M:%S", time());
		ob_start();
		$cron_script = CRON_PATH . '/' . $this->_cron_name . '.cron.php';
		if(file_exists($cron_script)) {
			$arguments = $this->arguments;

			require($cron_script);
		}
		else {
			# XXX throw exception			
			print "Error: Script with name '".$this->_cron_name."' not found\n";
			print "Looking at $cron_script\n";
		}
		
		$cont = ob_get_contents();
		ob_end_flush();
		$stop_mysqltime = strftime("%Y-%m-%d %H:%M:%S", time());
		$exec = microtime(true) - $t;
		$user = addslashes(`whoami`).":".DOMAIN;
		query("INSERT INTO cron_log (start, stop, script, exec_time, output, user) VALUES ('$start_mysqltime','$stop_mysqltime','".$this->_cron_name."', '$exec', '".addslashes($cont)."', '$user')");
	}
}

?>
