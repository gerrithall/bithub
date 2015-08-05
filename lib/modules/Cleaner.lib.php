<?php
/*
 * Cleaner.lib.php
 * Clean unsanitized inputs.
 * 
 * copyright 2015 zcor
 */


class Cleaner {
	protected static $current;
	protected $stored;
	
	public static function get_current() {
		if(!isset(self::$current)) {
			self::$current = new Cleaner();
		}
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class Cleaner.');
		}
		self::$current = $this;
	}

	/**
	 * Sanitize tainted data.
	 */

	public static function sanitized($filter_type,$value,$filter='') {
		# inputs are unsanitized until proven sanitized
		$sanitized = false;

		switch($filter_type) {
			case CTYPE_ARRAY:
				if(!is_array($filter)) {
					throw new BadTypeException('Passed filter should be an array.');
				}
				$filter = array_map("strval", $filter);
				if(in_array(strval($value),$filter)) {
					$sanitized = true;
				}
				break;

			case CTYPE_REGEX:
				if(preg_match($filter,$value)) {
					$sanitized = true;
				}

				break;

			case CTYPE_INT:
				if(ctype_digit((string)$value)) {
					$sanitized = true;
				}

				break;

			case CTYPE_BOOL:
				if($value == 0 || $value == 1) {
					$sanitized = true;
				}

				break;

			case CTYPE_ID:
				# positive non-zero integer value
				if(ctype_digit((string)$value) && $value > 0) {
					$sanitized = true;
				}

				break;


			case CTYPE_POS_INT:
				# positive integer value
				if(ctype_digit((string)$value) && $value >= 0) {
					$sanitized = true;
				}

				break;

			case CTYPE_POS_NUMERIC:
				# positive numeric value
				if(is_numeric($value) && $value > 0) {
					$sanitized = true;
				}

				break;

		}
	
		return $sanitized;
	}

}
?>
