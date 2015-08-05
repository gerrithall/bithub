<?php
/*
 * Scout.lib.php
 * Singleton class for managing the current URL state (directories and GET params)
 *
 * An instance of Scout parses the URL and stores information about what that URL is.
 * 
 * copyright 2015 zcor
 */
class Scout {
	protected static $current;

	# url parameters
	public $up;

	public static function get_current() {
		return self::$current;
	}
	
	public function __construct() {
		if(isset(self::$current)) {
			throw new ConstructionException('Cannot construct more than one instance of singleton class Scout.');
		}
		$this->_parse_url();
		self::$current = $this;
		
		#$user = new user();
		#Gbl::store('user',$user);
		Gbl::store('subdomain',$this->_parse_subdomain());
	}

	private function _parse_subdomain() {
		$domain_req = explode(".",$_SERVER['HTTP_HOST']);
	
		return($domain_req[0]);	
	}
	private function _parse_url() {
		$this->up = explode('?',$_SERVER['REQUEST_URI']);
		$this->up = explode('/',$this->up[0]);
		# get rid of that leading slash
		array_shift($this->up);
	}

	public function parameter_count($num) {
		if(sizeof($this->up) == $num+1) {
			return true;
		}
		return false;
	}

	public function get_controller() {
		if(SITE_AVAILABLE) {
			$controllers_array = Gbl::get('controllers_array');
			if(!empty($this->up[0]) && Cleaner::sanitized(CTYPE_ARRAY,$this->up[0],array_keys($controllers_array))) {
				$controller_name = $controllers_array[$this->up[0]];
			} 
			else {
				# we are running from the default page of the site, so pass the default back
				$controller_name = Gbl::get('default_controller');
			}
		}
		else {
			$controller_name = 'MaintenanceController';
		}
		
		# do we have to include the code for the controller?
		if(!is_object($controller_name)) {
			require_once(CONTROLLERS_PATH . '/' . $controller_name . '.lib.php');
		}
		
		return new $controller_name;
	}

}
?>
