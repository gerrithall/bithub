<?php
/*
 * LogoutController.lib.php
 * Destroy!
 *
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Github.lib.php');

class LogoutController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'home';
		parent::__construct();
	}
	
	public function run() {
		$G = new Github;
		$G->logout();	
	}
	
	protected function process_github($hub) {
		header("Content-type: text/plain");
		$G = new Github($hub);
		$r = $G->get_admin_repos();
		//$r = $G->get_contributors();
		print_r($G->curlinfo);
		print_r($G->header);
		print_r($G->json_response);
		print_r($G->json_error);

		die();	
		
	}

}	

?>
