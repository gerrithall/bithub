<?php
/*
 * LoginController.lib.php
 * Constructs and displays the home page.
 *
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Github.lib.php');

class LoginController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'auth';
		parent::__construct();
	}
	
	public function run() {
		$G = new Github;
		if($_GET['code']) {
			$G->authenticate($_GET['code']);
		}
		
		header("Location: " . SITE_URL . '/dashboard');	

		die();
	}
	
}	

?>
