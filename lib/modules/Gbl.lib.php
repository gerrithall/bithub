<?php
/*
 * Gbl.lib.php
 *
 * The full scope of variables. Basically a hash.
 * 
 * copyright 2015 zcor
 */

class Gbl {
	protected static $current;
	protected $stored;
	
	public static function get_current() {
		if(!isset(self::$current)) {
			self::$current = new Gbl();
		}
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class Gbl.');
		}
		self::$current = $this;
	}

	/**
	 * Register a variable with the Gbl.
	 *
	 * Can specify that current values should not be overwritten should they exist.
	 */
	public static function store($name, $value, $dont_overwrite=0) {
		$self = Gbl::get_current();
		if(!$dont_overwrite || !isset($self->stored[$name])) {
			$self->stored[$name] = $value;
		}
	}


	/**
	 * Retreive a variable from the Gbl.
	 */
	public static function get($name,$default=false) {
		$self = Gbl::get_current();
		if(isset($self->stored[$name])) {
			return $self->stored[$name];
		}
		else {
			return $default;
		}
	}

}
?>
