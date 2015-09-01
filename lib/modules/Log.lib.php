<?php
/**
 * Log.lib.php
 * 
 * copyright 2015 zcor
 */

class Log {
	private $_log_filename;
	private $_log_handle;
	private $_log_level;

	public function __construct($filename,$log_level) {
		if(empty($filename))
			throw new ConstructionException('Log requires a filename to log to.');

		$this->_log_filename = $filename;

		$this->_log_level = $log_level;
	}

	public function __destruct() {
		if(!empty($this->_log_handle))
			fclose($this->_log_handle);
	}

	public function write($log_level,$message='') {
		if($message && $log_level <= $this->_log_level) {
			
			$message = "[".$log_level."] ". strftime("%X %x", time()). "\n".$message . "\n";
			file_put_contents($this->_log_filename, $message, FILE_APPEND | LOCK_EX);
			$E = new Email;
			$E->queue_email(ERROR_EMAIL, 'Bithub', 'dev@gobithub.com', 'Bithub: ('.$this->_log_level.')', $message, $message);
		}
	}
}
?>
