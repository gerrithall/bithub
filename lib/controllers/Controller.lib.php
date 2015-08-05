<?php
/*
 * Controller.lib.php
 * Parent class of the controllers. The children of this class will control all passes through the scripts.
 *
 * copyright 2015 zcor
 *
 *
 */

abstract class Controller {
	protected $_fetch_template = 0;
	
	# load user auth state
	public function __construct() {}
    	
	# load all data that will be needed to display every page 
	# this should only be data that wont be cached 
	abstract protected function _initialize();
	
	protected function _process($processor) {
		$state = $this->state;

		require_once(INCLUDE_PATH . '/processors/' . $processor);
		if($this->state != $state)
			Gbl::get('system_log')->write(LOG_LEVEL_ALL,'Processing data from ' . $request_processor . ' modified current state.');
			
		$this->state = $state;
	}

	protected function _load($template_name, $cache_timeout=86400, $cache_id='') {
		$template_engine = Gbl::get('template_engine');

		# check for cached version of the template by the cache id (which can be empty)
		if(!$template_engine->is_cached($template_name, $cache_id)) {
			$template_engine->cache_lifetime = $cache_timeout;
		}
		
		//$template_engine->assign('user',Gbl::get('user'));			

		# set up the variables for the template engine
		if(!empty($this->state)) {
			foreach($this->state as $vn => $vv) {
				$template_engine->assign($vn,$vv);
			}
		}

		# fetch or display the template
		if($this->_fetch_template) {
			return $template_engine->fetch($template_name, $cache_id);
		}
		else {
			$template_engine->display($template_name, $cache_id);
			return true;
		}
	}

}

?>
