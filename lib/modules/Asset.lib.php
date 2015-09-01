<?php
/*
 * Asset.lib.php
 * Manage Colored Coin Assets
 * 
 * copyright 2015 zcor
 */


require_once(MODULES_PATH . '/Github.lib.php');
class Asset {

	public function __construct($h) {
	}
	public function retrieve_asset_info($a) {
		$ret = $this->coinprism_call('assets/'.$a, '');
		die($ret);
	}
	public function store_asset($repo) {
		$G = new Github;
		$ret = $G->load_repo($repo);
		if(!$ret) {
			return 0;
		}
		$public_id = HOTWALLET_ASSET; // Store a temp asset id, then update it later I guess
		
		//Only place we set the repo to public
		if(!$G->json_response->private) {
			query("UPDATE repo SET public = 1 WHERE name = '".escape($repo)."'");
		}

		$qi = "INSERT INTO asset (public_id, name_short, name, contract_url, issuer, description, description_mime, type, divisibility, link_to_website, icon_url, image_url, version, date_created) VALUES 
				(
					'" . $public_id . "',
					'" . escape( substr($G->json_response->name, 0, 10) ) . "',
					'" . escape( $G->json_response->full_name ) . "',
					'" . escape( SITE_URL . '/asset/' . $G->json_response->full_name ) ."',
					'" . escape( $G->json_response->owner->login ) . "',
					'" . escape( $G->json_response->description . "\n" . $G->json_response->html_url ) . "',
					'text/x-markdown; charset=UTF-8',
					'Bithub', 
					'".DEFAULT_DIVISIBILITY."', 
					'false',
					'" . escape( $G->json_response->owner->avatar_url ) ."', 
					'" . escape( $G->json_response->owner->avatar_url ) . "', 
					'1.0', 
					NOW()
					)";
			query($qi);
			return(mysqli_insert_id(Gbl::get('db')));
	}
	protected function store_asset_id($repo) {
		foreach($this->json_response->outputs AS $k => $v) {
			if(strlen($v->asset_id)>1) {
				if(!query_count("SELECT * FROM asset WHERE name = '" . escape($repo) ."'")) {
					$this->store_asset($repo);
				}
				query("UPDATE asset SET public_id = '" . escape($v->asset_id) . "' WHERE name='" . escape($repo) . "'");	
				if(query_count("SELECT * FROM asset WHERE public_id = '".$v->asset_id."'") == 1) {
					return 1;
				}
			}
		}
		return 0;
	}
	public function display_asset_definition($repo) {
		$query = "SELECT * FROM asset WHERE asset_id = '" . escape($repo) . "'"; 
		if(!query_count($query)) {
			$this->store_asset($repo);
		}
		$r = query_assoc($query);
		if(!is_array($r)) {
			die("{'error':'No Asset'}");
		}
		$arr = array(
				'asset_ids' => array( $r['public_id']),
				'contract_url' => $r['contract_url'],
				'name_short' => $r['name_short'],
				'name' => $r['name'],
				'issuer' => $r['issuer'],
				'description' => $r['description'],
				'description_mime' => $r['description_mime'],
				'type' => $r['type'],
				'divisibility' => $r['divisibility'],
				'link_to_website' => $r['link_to_website'],
				'icon_url' => $r['icon_url'],
				'image_url' => $r['image_url'],
				'version' => $r['version']
		);
		return(json_encode($arr));

	}
	public function create_coinprism_address($label) {
		if(!$label) $label = "Bithub ".strftime("%X %x", time());

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/account/createaddress");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "{
		  \"alias\": \"$label\"
		  }");

		  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Content-Type: application/json",
		    "X-Coinprism-Username: ".COINPRISM_USERNAME,
		    "X-Coinprism-Password: ".COINPRISM_PASSWORD
		));
		
		$response = curl_exec($ch);
		
		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($response, $header_size);

		$arr = json_decode($ret);
		$this->json_response = $arr;
		$this->json_error = json_last_error();
		$this->raw_response = $ret;
		$this->dump_log();
		if(!$this->json_error && strlen($this->json_response->bitcoin_address) > 1) {
			$q = "INSERT INTO wallet (bitcoin_address, asset_address, private_key_hash, repo, user_id, created,last_sniff_date, confirmations) VALUES 
				( 
					'" . escape($this->json_response->bitcoin_address) . "', 
					'" . escape($this->json_response->asset_address) . "', 
					'" . escape( $this->hash_key( $this->json_response->private_key ) ) . "', 
					'" . escape( $label ) . "',
					'" . escape($_SESSION['user']['github_id']) . "',
					NOW(),NOW(),
					-1
				)";
			query($q);
			return(1);
		}
		return(0);
		curl_close($ch);
	}
	protected function hash_key($k) {
		//XXX Need a security expert to make this work
		return($k);

	}
	protected function unhash_key($k) {
		return($k);
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
	protected function coinprism_call($endpoint, $post, $log = 1) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/$endpoint?format=json");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Content-Type: application/json"
			));

		$response = curl_exec($ch);

		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($response, $header_size);

		$arr = json_decode($ret);
		$this->json_response = $arr;
		$this->json_error = json_last_error();
		$this->raw_response = $ret;
		
		if($log) $this->dump_log();
		if($this->json_response->Message == 'Error') {
			Gbl::get('system_log')->write(LOG_LEVEL_ERROR, "Coinprism Error\n".print_r($this->json_response,1));
			die("API ERROR: ".$this->json_response->ErrorCode);
		}
		if(!$this->json_error) {
			return($ret);
		} else {
			Gbl::get('system_log')->write(LOG_LEVEL_ERROR, "Bithub Error\n".print_r($this->json_response,1));
			die($this->json_error);
		}

	}
	public function get_repo_id($repo) {
		$q = query_assoc("SELECT * FROM asset WHERE name = '".escape($repo)."'");
		if($q['asset_id'] > 0) {
			return($q['asset_id']);
		}
		$ret = $this->store_asset($repo);
		if($ret > 0) return $ret;
		
		die("{'Error': 'Error retrieving asset'}");
	}
	public function issue_asset($repo) {
			
		$wallet = query_assoc("SELECT * FROM wallet WHERE repo = '".escape($repo)."'");
		$fee = DEFAULT_MINER_FEE;
		$fee = 30000;
		$repo_id = $this->get_repo_id($repo);
		$pf = "{
			  \"fees\": " . $fee . ",
    			  \"from\": \"" . $wallet['bitcoin_address'] ."\",
      			  \"address\": \"". $wallet['asset_address'] ."\",
 
			  \"amount\": \"". pow(10, DEFAULT_DIVISIBILITY)  ."\",   
			  \"metadata\": \"u=http://gobithub.com/a/". $repo_id  ."\"
			}";
		$ret = $this->coinprism_call('issueasset', $pf);
		
		if(!$this->store_asset_id($repo)) {
			die("{'error': 'Invalid Asset Id'}");
		}
		
		$hex = $this->make_raw($ret);
		$pf = '{ 
				"transaction": "'.$hex.'",
				"keys": [
					"' . $this->unhash_key($wallet['private_key_hash']) . '"
				]
			}';
		$ret = $this->coinprism_call('signtransaction', $pf);

		$j = json_decode($ret);
		$pf = '"'.$j->raw.'"';
		
		$ret = $this->coinprism_call('sendrawtransaction', $pf);
		if(substr($repo,0,1) == '"' && substr($repo,-1,1) == '"') $repo = substr($repo,1,strlen($repo)-2);
		query("UPDATE asset SET transaction_hash = '" . $ret . "' WHERE name = '" . escape($repo) . "'");	
		return($ret);
	}
	public function sniff_assets() {
		$q = query("SELECT * FROM asset WHERE status =0 AND transaction_hash != ''");
		while($r = fa($q)) {
			$hash = $r['transaction_hash'];
			if(substr($hash,0,1) == '"' && substr($hash,-1,1) == '"') $hash = substr($hash,1,strlen($hash)-2);
			$addr[] = $hash;
		}
		$url = "http://btc.blockr.io/api/v1/zerotx/info/".implode($addr,",");
		if(count($addr) < 1) {
			return;
		}
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
		//XXX Hacky solution... but eff it, ship it
		if(count($arr->data)>1) {
		  foreach($arr->data AS $k => $v) {
			if($v->is_confirmed) {
				$h = explode('/', $v->info_redirect);
				$th = array_pop($h);
				query("UPDATE asset SET status = 1, confirmations = 6, transaction_hash = '".escape($th)."' WHERE transaction_hash LIKE '%".escape($th)."%'");
				$info = query_assoc("SELECT * FROM asset WHERE transaction_hash = '".escape($th)."'");
				query("UPDATE repo SET status = 4, status_date = NOW() WHERE name = '".$info['name']."'");
			}
		  }
		} else {
			if($arr->data->is_confirmed) {
				$h = explode('/', $arr->data->info_redirect);
				$th = array_pop($h);
				query("UPDATE asset SET status = 1, confirmations = 6, transaction_hash = '".escape($th)."' WHERE transaction_hash LIKE '%".escape($th)."%'");
				$info = query_assoc("SELECT * FROM asset WHERE transaction_hash = '".escape($th)."'");
				query("UPDATE repo SET status = 4, status_date = NOW() WHERE name = '".$info['name']."'");
			}
	
		}
	}

		function varint($siglen) {
			if($siglen < 256) {
				$encoded_siglen = bin2hex(pack("C", $siglen)); 
			} else {
				die("Not supported");
			}
			return($encoded_siglen);

		}
		function make_raw($i) {
			$j = json_decode($i);	
			$ret = "01000000"; // 4 bytes version
			$ret .= "01"; // number of inputs
			$lh = $j->inputs[0]->output_hash;
			$ret .= bin2hex(strrev(hex2bin($lh)));  // Must reverse transaction because MATHS
			$ret .= bin2hex(pack("V", $j->inputs[0]->output_index)); // Little Endian unsigned long encoding of output index;
			
			//script sig
			$sig = $j->inputs[0]->script_signature;
			$encoded_siglen = $this->varint( strlen( hex2bin($sig) ) );
			$ret .= $encoded_siglen . $sig;	

			$ret .= "ffffffff"; //Sequence
			
			$num_out =  count($j->outputs);
			$ret .= $this->varint($num_out);

			//loop through outputs
			foreach($j->outputs AS $k => $v) {
				$amount = $v->value;
				$ret .= bin2hex(pack("q", $amount));
				
				//sign scripts as per above
				$sig = $v->script;
				$ret .= $this->varint( strlen( hex2bin($sig))) . $sig;

			}

			$ret .= "00000000"; //locktime
			return($ret);
		}
		function blockcypher() {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://api.blockcypher.com/v1/bcy/test/txs/push");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);

		curl_setopt($ch, CURLOPT_POST, TRUE);
		$pf = '{"inputs":[{"output_hash":"4016b3c256305f759e448888bb3690a00c0077c1c0a2039bb8b4828a22c5dfb1","output_index":0,"value":1000000,"addresses":["1F1HBDk5TNiP2sLipuJ91cZcsD1T8KaUux"],"script_signature":"76a914999f43a1b32c7897343e1d3ef7ba7c0aec0fd3a988ac","asset_id":null,"asset_quantity":null}],"outputs":[{"index":0,"value":600,"addresses":["1F1HBDk5TNiP2sLipuJ91cZcsD1T8KaUux"],"script":"76a914999f43a1b32c7897343e1d3ef7ba7c0aec0fd3a988ac","asset_id":"AMS2NSbqrs2ZzNjLMCuyf2hNPwb43ysYsA","asset_quantity":"500"},{"index":1,"value":0,"addresses":[],"script":"6a254f41010001f4031d753d687474703a2f2f676f6269746875622e636f6d2f61737365742f31","asset_id":null,"asset_quantity":null},{"index":2,"value":998400,"addresses":["1F1HBDk5TNiP2sLipuJ91cZcsD1T8KaUux"],"script":"76a914999f43a1b32c7897343e1d3ef7ba7c0aec0fd3a988ac","asset_id":null,"asset_quantity":null}],"amount":999000,"fees":1000}';
		//header("Content-type: text/plain"); print_r(json_decode($pf));die();
		$pfhex = $this->make_raw($pf);
//		$pfhex = "01000000011935b41d12936df99d322ac8972b74ecff7b79408bbccaf1b2eb8015228beac8000000006b483045022100921fc36b911094280f07d8504a80fbab9b823a25f102e2bc69b14bcd369dfc7902200d07067d47f040e724b556e5bc3061af132d5a47bd96e901429d53c41e0f8cca012102152e2bb5b273561ece7bbe8b1df51a4c44f5ab0bc940c105045e2cc77e618044ffffffff0240420f00000000001976a9145fb1af31edd2aa5a2bbaa24f6043d6ec31f7e63288ac20da3c00000000001976a914efec6de6c253e657a9d5506a78ee48d89762fb3188ac00000000";
		$post = '{"tx":"'.$pfhex.'"}';

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Content-Type: application/json"
			));
		
		$response = curl_exec($ch);

		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($response, $header_size);

		$arr = json_decode($ret);
		$this->json_response = $arr;
		$this->json_error = json_last_error();
		$this->raw_response = $ret;
		$this->dump_log();	

		curl_close($ch);
		
		header("Content-type: text/plain");
			
		var_dump($ret);

		die();
	}
	public function coloredcoins_api($address){
//		var bitcoin = require('bitcoinjs-lib');
		//var request = require('request');

		$address1 = '1F1HBDk5TNiP2sLipuJ91cZcsD1T8KaUux';
		$amount = .01 * 100000000;

		$g = query_assoc("SELECT * FROM asset WHERE asset_id = '1'");
    		$url = 'http://testnet.api.coloredcoins.org:80/v2/issue';
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);

		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
						
// send parameters
$asset = "{
    \"issueAddress\": \"$address1\",
    \"amount\": $amount,
    \"divisibility\": ".$g['divisibility'].",
    \"fee\": 1000,
    \"transfer\": [{
	\"address\": \"$address1\",
	\"amount\": $amount
    }],
    \"metadata\": {
        \"assetId\": \"1\",
        \"assetName\": \"Asset Name\",
        \"issuer\": \"Asset Issuer\",
        \"description\": \"My Description\",
        \"userData\": {
            \"meta\" : [
                {\"key\": \"Item ID\", \"value\": 2, \"type\": \"Number\"},
                {\"key\": \"Item Name\", \"value\": \"Item Name\", \"type\": \"String\"},
                {\"key\": \"Company\", \"value\": \"My Company\", \"type\": \"String\"},
                {\"key\": \"Address\", \"value\": \"San Francisco, CA\", \"type\": \"String\"}
            ]
        }
    }
}";
//header("Content-type: text/plain"); print_r(json_decode($asset);die();
//die($asset);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $asset);
		$response = curl_exec($ch);
		
		$this->curlinfo = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$this->header = substr($ret, 0, $header_size);
		$ret = substr($response, $header_size);

		die($ret);

	}
	//XXX This needs to be stored when we generate an address, methinks
	public function process_new_assets() {
		$q = query("SELECT * FROM address WHERE status >= 6 and asset_id <1");
		while($r = fa($q)) {
			
			$this->store_asset($r['repo']);	

			die();
		}
	}


}
?>
