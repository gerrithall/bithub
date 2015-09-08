<?php
/*
 * TermsController.lib.php
 * Constructs and displays the terms of service.
 *
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Github.lib.php');

class TermsController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'home';
		parent::__construct();
	}
	
	public function run() {
		$this->_load("terms.html");
	}

}	

?>
