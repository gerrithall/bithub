<?php
/*
 * EmailTemplateController.lib.php
 * To format HTML emails for sending
 *
 * copyright zcor
 */

class EmailTemplateController extends Controller {
	public $state;

	public function __construct() {
		$this->_initialize();
	}

	protected function _initialize() {
		$this->state['page'] = 'admin';
		parent::__construct();
	}

	public function load_email($email, $vars, $txt=0) {
		foreach($vars AS $k => $v) {
			$this->state[$k] = $v;
		}
		ob_start();
		$this->_load("email/$email.html");
		$c = ob_get_contents();
		ob_end_clean();
		if($txt) {
//			$txtemail = html_entity_decode($c);
			$txtemail = strip_tags($c);
			$txtemail = html_entity_decode($txtemail);
			$txtemail = preg_replace("/(\t)/", "", $txtemail);
					$txtemail = preg_replace("/(\n){2,}/", "\n\n", $txtemail);

			return($txtemail);
		}
		return($c);
	}

	public function run() {
		die();
	}
}
?>
