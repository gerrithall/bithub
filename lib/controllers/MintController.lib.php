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
			$repo_owner = $this->state['repo']['user_id'];
				
			$this->state['is_owner'] = 0;
			if(is_numeric($repo_owner) && $repo_owner > 0 && $_SESSION['user']['github_id'] == $repo_owner) {
				$this->state['is_owner'] = 1;
				if(is_numeric($_POST['kick_offset']) && $_POST['kick_offset'] > 0) {
					$G->kick_start_date($_POST['kick_offset']);
				}
			}

	
			if(!$this->state['repo']['public']) {
				$G->load_user();
			}
			$G->load_coin_owners($project_name);

			
			$this->state['date_minted'] = $G->date_minted;
			$this->state['deposit'] = $G->deposit_amount;	

			$this->state['last_commit'] = strftime("%b %d", strtotime(query_grab("SELECT max(commit_date) FROM commit_log WHERE repo = '".$this->state['repo']['name']."'")));
			$this->state['asset_hash'] = $G->asset_hash;
			$this->state['cal'] = $G->schedule;
			$this->state['contributors'] = $G->contributors;
			$this->state['share'] = $G->share;
			$this->state['today'] = $G->today;
					
			if($this->state['is_owner']) {
				$this->state['valid_coin_change_dates'] = $G->get_valid_coin_change_dates();
			}
			if($paths[4] == 'json') {
				header("Content-type: text/json");
				echo json_encode(array('name' => $project_name,
							'date_minted' => $G->date_minted,
							'asset_url' => SITE_URL.'/a/'.$G->asset_id,
							'asset_hash' => $G->asset_hash,
							'coin_ownership' => $G->share,
							'commits' => $G->contributors,
							'deposit' => $G->deposit,
							'last_commit' => $G->last_commit,
							'commit_log' => $G->schedule
							), JSON_PRETTY_PRINT
							);
				die();	
			}
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
