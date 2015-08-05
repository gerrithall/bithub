<?php
/*
 * CookieStore.lib.php
 */


class CookieStore {
	protected static $current;
	protected $_stored;
	protected $_destroyed;
	protected $_loaded;

	# the ids of already saved cookies
	protected $_saved;
	
	public static function get_current() {
		if(!isset(self::$current)) {
			self::$current = new CookieStore();
		}
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class Sentinel.');
		}
		self::$current = $this;
	}

	/**
	 * Register a variable with the CookieStore store.
	 *
	 * Can specify that current values should not be overwritten should they exist.
	 */
	public static function store($ckey, $cval, $dont_overwrite=0) {
		$self = CookieStore::get_current();
		if(!$dont_overwrite || !isset($self->_stored[$ckey])) {
			$self->_stored[$ckey] = $cval;
			$self->_loaded[$ckey] = 1;
		}
	}


	/**
	 * Retreive a variable from the CookieStore store.
	 */
	public static function get($name) {
		$self = CookieStore::get_current();
		$self->_load(array($name));
		if(isset($self->_stored[$name])) {
			return $self->_stored[$name];
		}
		else {
			return '';
		}
	}


	/**
	 * Destroys a specific cookie
	 */
	public static function destroy($ckey) {
		$self = CookieStore::get_current();
		if(isset($self->_stored[$ckey])) {
			unset($self->_stored[$ckey]);
			$self->_destroyed[$ckey] = 1;
		}
	}


	/**
	 * Loads and decodes cookie data.
	 */
	protected function _load($cstore_array) {
		foreach($cstore_array as $ckey) {
			if(empty($this->_loaded[$ckey]) && !empty($_COOKIE[$ckey])) {
				$this->_stored[$ckey] = $this->_from_cookie_val($_COOKIE[$ckey]);
			}
			$this->_loaded[$ckey] = 1;
		}
	}


	/**
	 * Encodes and saves cookie data.
	 */
	public static function save() {
		$self = CookieStore::get_current();
		$expiration_date = time() + 30*60;
		if(!empty($self->_stored)) {
			foreach($self->_stored as $ckey => $cval) {
				if(empty($self->_saved[$ckey])) {
					setcookie($ckey, $self->_to_cookie_val($cval), $expiration_date, '/', DOMAIN);
					$self->_saved[$ckey] = 1;
				}
			}
		}
		if(!empty($self->_destroyed)) {
			foreach($self->_destroyed as $ckey => $cval) {
				setcookie($ckey, 0, time() - 86400, '/', DOMAIN);
			}
		}
	}


	/**
	 * Converts a PHP value into a cookie value.
	 */
	private function _to_cookie_val($value) {
		if(is_array($value) || is_string($value) || is_float($value)) {
			return(serialize($value));
			//return json_encode($value);
		}
		else {
			return '';
		}
	}


	/**
	 * Decodes a cookie value into a PHP value.
	 */
	private function _from_cookie_val($value) {
		return(unserialize($value));
		//return json_decode($value, true);
	}

}
?>
