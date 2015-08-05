<?php
/*
 * HomeController.lib.php
 * Constructs and displays the home page.
 *
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Github.lib.php');

class HomeController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'home';
		parent::__construct();
	}
	
	public function run() {
		if($_SESSION['token']) {
			header("Location: ".SITE_URL."/dashboard");
			die();
		}
		if($_POST) {
			$this->process_github($_POST['hub']);
		}
		$G = new Github;
		$this->state['featured'] = $G->get_featured_projects();
		$this->state['github_redirect'] = SITE_URL.'/auth';
		$this->_load("home.html");
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
