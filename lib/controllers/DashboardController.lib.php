<?php
/*
 * DashboardController.lib.php
 * Constructs and displays the home page.
 *
 * copyright zcor
 */

require_once(MODULES_PATH . '/Github.lib.php');

class DashboardController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'dash';
		parent::__construct();
	}
	
	public function run() {
		//require_once(MODULES_PATH . '/Asset.lib.php');
		//$A = new Asset;
		//$A->issue_asset('moloco/dash');
		
		
		$G = new Github;
		$G->load_user();
		$G->load_repos();
		
		$arr = array();
		foreach($G->repos AS $k => $v) {
			if($v->owner->id == $_SESSION['user']['github_id']) {
				$owner = "You";
			} else {
				$owner = $v->owner->login;
			}
			$arr[] = array('id' => $v->id,
					'name' => $v->name,
					'fullname' => $v->full_name,
					'dateraw' => strtotime($v->updated_at),
					'date' => strftime("%x", strtotime($v->updated_at)),
					'url' => $v->html_url,
					'owner' => $owner,
					'status' => $v->status,
					'confirmations' => $v->confirmations,
					'address' => $v->address,
					'transaction_hash' => $v->transaction_hash
					);
		}
		
		usort($arr, "arrsort");
		$this->state['debug'] = print_r($G->repos,1);
		$this->state['repos'] = $arr;
		$this->_load("dash.html");
	}
}	

function arrsort($a, $b) {
	if($a['status'] != $b['status']) {
		return($a['status'] < $b['status']) ? +1 : -1;
	}
	if($a['dateraw'] == $b['dateraw']) return 0;
	return ($a['dateraw'] < $b['dateraw']) ? +1 : -1;

}

?>
