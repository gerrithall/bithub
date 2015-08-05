<?php
/**
 * HTTP.lib.php
 * Singleton class acting as an interface to HTTP.
 * Provides wrappers for redirections, cookie settings, etc.
 * 
 * copyright 2015 zcor
 */

class HTTP {
	protected static $current;

	public static function get_current() {
		if(!isset(self::$current)) {
			self::$current = new HTTP();
		}
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class HTTP.');
		}
		self::$current = $this;
	}

	public static function http_setcookie($cookie_name, $cookie_val, $cookie_expire, $cookie_path, $cookie_domain) {
		if(setcookie($cookie_name, $cookie_val, $cookie_expire, $cookie_path, $cookie_domain)) {
			return true;
		}
		else {
			return false;
		}
	}

	public static function http_redirect($redirect_location, $http_code='') {
		if($http_code) {
			header("Location: " . $redirect_location, $http_code);
		}
		else {
			header("Location: " . $redirect_location);
		}
		exit;
	}


}
?>
