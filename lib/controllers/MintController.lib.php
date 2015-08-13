<?php
/*
 * MintController.lib.php
 * Constructs and displays the home page.
 *
 * copyright zcor
 */

require_once(MODULES_PATH . '/Bitcoin.lib.php');
require_once(MODULES_PATH . '/Github.lib.php');

class MintController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'mint';
		
		parent::__construct();
	}
	
	public function run() {
		$G = new Github;

		$req_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$path = parse_url($req_url, PHP_URL_PATH);
		$paths = explode("/", $path);
		$project_name = $paths[2].'/'.$paths[3];

		$this->state['project_name'] = $project_name;
		
		$this->state['repo'] = $G->load_stored_repo($project_name);
		if($this->state['repo']['status'] == 4) {
			if(!$this->state['repo']['public']) {
				$G->load_user();
			}
			$G->load_coin_owners($project_name);
			$this->state['date_minted'] = $G->date_minted;
			$this->state['last_commit'] = strftime("%b %d", strtotime(query_grab("SELECT max(commit_date) FROM commit_log WHERE repo = '".$this->state['repo']['name']."'")));
			$this->state['asset_hash'] = $G->asset_hash;
			$this->state['cal'] = $G->schedule;
			$this->state['contributors'] = $G->contributors;
			$this->state['equity'] = $G->equity;
			$this->state['today'] = $G->today;

		} else {
		
		$G->load_user();
		$BTC = new Bitcoin;	
		$BTC->generate_address($project_name);

		$this->state['btc_address'] = $BTC->input_address;
		$this->state['status'] = $BTC->status;
		$this->state['hash'] = $BTC->transaction_hash;

		}
		$this->_load("mint.html");
		
	}
}	

?>
