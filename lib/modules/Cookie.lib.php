<?php
/*
 * Cookie.lib.php
 */

class Cookie {
	protected $_name;
	protected $_value;

	public function __construct($name) {
		$this->_name = $name;
		$this->_load();
	}

	/**
	 * Get the data from the current cookie
	 */
	private function _load() {
		$this->_value = CookieStore::get($this->_name);
		if(empty($this->_value) || !is_array($this->_value)) {
			$this->_value = array();
		}
	}

	/**
	 * Add values to the store
	 */
	public function add($key, $val) {
		$this->_value[$key] = $val;
	}

	/**
	 * Remove values from the store
	 */
	public function remove($key) {
		if(isset($this->_value[$key])) {
			unset($this->_value[$key]);
		}
	}

	/**
	 * Get values from the store
	 */
	public function get($key) {
		if(isset($this->_value[$key])) {
			return $this->_value[$key];
		}
		return false;
	}

	/**
	 * Save all changes
	 */
	public function save() {
		CookieStore::store($this->_name, $this->_value);
		CookieStore::save();
	}

	/**
	 * Destroy cookie
	 */
	public function destroy() {
		CookieStore::destroy($this->_name);
		CookieStore::save();
	}

	public function values() {
		return $this->_value;	
	}


}
?>
