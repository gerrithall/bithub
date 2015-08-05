<?php
/*
 * BTCController.lib.php
 * Process payment
 *
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Bitcoin.lib.php');
require_once(MODULES_PATH . '/Github.lib.php');
require_once(MODULES_PATH . '/Asset.lib.php');

class BTCController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'home';
		parent::__construct();
	}
	
	public function run() {
		header("Content-type: text/plain");
		$req_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$path = parse_url($req_url, PHP_URL_PATH);
		$paths = explode("/", $path);
		$secret = $paths[2];	

		$BTC = new Bitcoin;
		//$BTC->generate_address();
		$BTC->process_incoming($_GET, $secret);
		if($_GET['confirmations'] >= 6) {
			echo "*ok*";		
			
			//XXX I'm worried this will be too slow to fire back an "OK" to blockchain, but cron is not firing reliably.
			$repo_name = query_grab("SELECT name FROM repo WHERE address = '".escape($_GET['input_address'])."'");
			
			$A = new Asset;
			$A->issue_asset($repo_name); 
		}
		die();
	}
	
	
}	

?>
