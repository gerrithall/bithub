<?php
/*
 * AssetController.lib.php
 * Displays Asset Definition.
 *
 * copyright zcor
 */


require_once(MODULES_PATH . '/Asset.lib.php');
class AssetController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'admin';
		parent::__construct();
	}
	
	public function run() {
		$A = new Asset;
		
		$req_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$path = parse_url($req_url, PHP_URL_PATH);
		$paths = explode("/", $path);
		$secret = $paths[2];	

		if(($paths[1] == 'a' || $paths[1] == 'asset') && is_numeric($paths[2]) && !$paths[3]) {
		
			$json = $A->display_asset_definition($paths[2]);
			header("Content-type: text/plain");
			die($json);
		}
		if(($paths[1] == 'asset' || $paths[1] == 'a') && strlen($paths[2]) > 0 && strlen($paths[3]) > 0) {
			$json = $A->display_asset_definition( query_grab("SELECT asset_id FROM asset WHERE name = '".escape($paths[2]."/".$paths[3])."'" ));	
			header("Content-type: text/plain");
			die($json);
		}
		//As a cron job, run the following
		//$A->process_new_assets();
		//It should create a new asset, store the asset_id in the address table, create an asset row in the asset table
		//Then, we're good?
//		$ret = $A->issue_asset('SandHillExchange/VCFriendFinder');

		//$A->retrieve_asset_info("AMS2NSbqrs2ZzNjLMCuyf2hNPwb43ysYsA");
		//$A->blockcypher();
//		$A->coloredcoins_api('issue');
		//$A->create_coinprism_address();
		die("Nope");
		$this->_load("admin.html");
	}
}	

?>
