<?php
/*
 * HomeController.lib.php
 * Constructs and displays the home page.
 *
 * copyright 2008 gerrit hall
 */


ini_set('display_errors',1);
class UnsubscribeController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'unsubscribe';
		parent::__construct();
	}
	
	
	public function run() {
/*
		echo "<pre>";
		ini_set('max_execution_time', 600);
		//$q = query("SELECT * FROM uploaded WHERE plaintext != '' ORDER BY rand() LIMIT 1000");
		$q = query("SELECT * FROM uploaded ORDER BY rand() LIMIT 500");
		$arr = array();	
		while($r = fetch_assoc($q)) {
			$q2 = query("SELECT * FROM seo_keywords");
			while($r2 = fetch_assoc($q2)) {
				if(preg_match("/".$r2['term']."/i", $r['plaintext'] )) {
					$arr[$r2['term']]++;
				}
			}
		}
		print_r($arr);
		foreach($arr AS $k => $v) {
			query("UPDATE seo_keywords SET pct_resumes_containing = '".($v )."' WHERE term = '".$k."'");
		}
		die();*/
		$a = $_SERVER['REQUEST_URI'];
		$pos = strpos($a, "&");
		if(strpos($a, "&")) {
			$junk = substr($a, $pos);
			$a = substr($a, 0, $pos);
		}
		
		$a = explode("/", $a);
		$hash = array_pop($a);
		query("INSERT INTO do_not_email (email, date_added) VALUES ('".addslashes(urldecode(trim($hash)))."', NOW())");
		
		$this->_load("unsubscribe.html");	
		
	}
}	


?>
