<?php
/*
 * Bitcoin.lib.php
 * Git Bits
 * 
 * copyright 2015 zcor
 */

require_once(MODULES_PATH . '/Asset.lib.php');

class Bitcoin {
	
	protected $receiving_address = BTC_WALLET_KEY;
	protected $callback_url = SITE_URL . '/btc';
	
	public function __construct($h) {
			
	}

	//Deprecated cause Blockchain.info sux
	public function process_incoming($g, $s) {
		query("INSERT INTO incoming (date_created, value, input_address, confirmations, transaction_hash, input_transaction_hash, destination_address, secret)
			       VALUES (
				NOW(), 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['value']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['input_address']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['confirmations']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['transaction_hash']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['input_transaction_hash']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $g['destination_address']) . "', 
				'" . mysqli_real_escape_string(Gbl::get('db'), $s) . "' 
			        )");
		
		query("UPDATE address SET status = '" . escape($g['confirmations']) . "' WHERE input_address = '" . escape($g['input_address'])  . "'");
		query("UPDATE repo SET status = 2, status_date=NOW() WHERE address = '". escape($g['input_address']) ."'");
	}

	protected function get_saved_address($repo) {
		$q = "SELECT * FROM wallet WHERE repo = '" .escape($repo) . "' AND user_id = '" . escape($_SESSION['user']['github_id']) . "'";
		$arr = query_assoc($q);
		if(is_array($arr)) {
			$this->input_address = $arr['bitcoin_address'];
			$this->status = $arr['confirmations'];
			$this->transaction_hash = $arr['last_transaction_hash'];
			query("UPDATE wallet SET created = NOW() WHERE  repo = '" .escape($repo) . "' AND user_id = '" . escape($_SESSION['user']['github_id']) . "'");
			return(1);
		}
		return(0);
	}

	//Deprecated cause Blockchain.info sux
	public function blockchain_addr() {
		$secret = substr(md5( rand() * time()),0,8);
		$callback_url = $this->callback_url . "/" . $secret;
		
		$url = "https://blockchain.info/api/receive?method=create&address=" . $this->receiving_address . "&callback=" . urlencode($callback_url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$ret = curl_exec($ch);
		
		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($ret, $header_size);

		$arr = json_decode($ret);
		$this->json_response = $arr;
		$this->json_error = json_last_error();
		$this->raw_response = $ret;
		
		$this->dump_log();
		if(!$this->json_error) {
			$this->callback_url = $this->json_response->callback_url;
			$this->input_address = $this->json_response->input_address;
			$this->status = -1;
			$this->destination = $this->json_response->destination;
			$this->fee_percent = $this->json_response->fee_percent;
			$this->secret = $secret;

			query("INSERT INTO address (date_created, status, callback_url, input_address, destination, fee_percent, secret, repo, user_id, asset_id) 
			       VALUES (
				NOW(), 
				'-1', 
				'" . escape($this->callback_url) . "', 
				'" . escape($this->input_address) . "', 
				'" . escape($this->destination) . "', 
				'" . escape($this->fee_percent) . "',
				'" . $secret . "',
				'" . escape($repo) . "', 
				'" . escape($_SESSION['user']['github_id']) . "',
				'-1'
			        )");

			return(1);
		}

	}
	
	//Arguably this should now be merged with Asset
	public function generate_address($repo) {
		if($this->get_saved_address($repo)) {
			return(1);	
		}
		$A = new Asset;
		$res = $A->create_coinprism_address($repo);
		if($res) {
			$this->receiving_address = $A->json_response->bitcoin_address;
			$this->input_address = $this->receiving_address;
			$this->status = -1;
			$this->hash = '';
			query("INSERT INTO repo (name, address, user_id, status, status_date) VALUES ('" . escape($repo) . "', '" . escape($this->receiving_address) . "', '" . escape($_SESSION['user']['github_id']) . "', '1', NOW())");
		}
		
		return(0);
	}

	public function sniff_wallets() {
		$q = query("SELECT * FROM wallet WHERE confirmations <6 AND TIMESTAMPDIFF(DAY, created, NOW()) < 5");
		while($r = fa($q)) {
			$addr[] = $r['bitcoin_address'];
			$data[$r['bitcoin_address']] = $r;
			echo $r['bitcoin_address']." ";
		}
		if(!count($addr)) {
			echo "No addresses";
			return;
		}
		$url = "http://btc.blockr.io/api/v1/address/info/".implode($addr,",")."?confirmations=0";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$ret = curl_exec($ch);
		
		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($ret, $header_size);

		$arr = json_decode($ret);
		$this->json_response = $arr;
		$this->json_error = json_last_error();
		$this->raw_response = $ret;
		
		if($arr->status != 'success') {
			echo "Error";
			print_r($arr);
		}
		foreach($arr->data AS $k => $v) {
		
			if(($v->nb_txs >0 && $v->first_tx->confirmations > $data[$v->address]['confirmations']) || ($data[$v->address]['confirmations'] == 0 && $v->first_tx->confirmations == 0 && $v->nb_txs ==1 && $v->balance > 0)) {
				echo "New activity detected on ".$v->address."\n<br/>\n";
				query("UPDATE wallet SET deposit_amount = '".escape($v->balance)."', confirmations = '".escape($v->first_tx->confirmations)."', last_transaction_hash = '".escape($v->first_tx->tx)."', last_sniff_date=NOW() WHERE bitcoin_address = '".escape($v->address)."'");

				$repo = query_assoc("SELECT * FROM repo WHERE address = '".escape($v->address)."'");
				if(!is_array($repo)) {
					echo "Error fetching repo";
				} elseif($v->first_tx->confirmations >= 6 && $repo['status'] < 3) {
					query("UPDATE repo SET status = 3 WHERE repo_id = '".$repo['repo_id']."'");
					$A = new Asset;
					$A->issue_asset($repo['name']);
				} else {
					if($repo['status'] == 1) {
						query("UPDATE repo SET status = 2 WHERE repo_id = '".$repo['repo_id']."'");
					}
				}
			} else {
				query("UPDATE wallet SET last_sniff_date = NOW() WHERE bitcoin_address = '".escape($v->address)."'");
			}
		}
//		print_r($addr);die();
	}

	public function check_status($addr) {
			

		$q = "SELECT * FROM incoming WHERE input_address = '" .escape($addr) . "' ORDER BY incoming_id DESC";
		if(!query_count($q)) {
			return(-1);
		} else {
			$arr = query_assoc($q);
			$this->transaction_hash = $arr['transaction_hash'];
			query("UPDATE address SET status = ' " . escape($arr['confirmations']) . "' WHERE input_address = '" . escape($addr) . "'");
			return($arr['confirmations']);
		}
		
	}
	protected function dump_log() {
		$log = "============\n" . strftime("%X %x", time()) . "\n" . $this->url . "\n";	
		$log .= print_r($this->curlinfo, 1);
		$log .= "\n\nheader ------\n";
		$log .= print_r($this->header, 1);
		$log .= "\n\njson ------\n";
		$log .= print_r($this->json_response, 1);
		$log .= "\n\nerror ------\n";
		$log .= print_r($this->json_error, 1);

		Gbl::get('system_log')->write(LOG_LEVEL_ALL, $log);
	}

}
?>
