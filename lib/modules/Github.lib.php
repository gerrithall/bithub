<?php
/*
 * Github.lib.php
 * Hit Git API
 * 
 * copyright 2015 zcor
 */


class Github {
	protected $hub;
	protected $useragent = GITHUB_USERAGENT;
	protected $token = GITHUB_TOKEN;
	protected $client_id = GITHUB_CLIENTID;
	protected $client_secret = GITHUB_CLIENTSECRET;

	public function __construct($h) {
	}
	public function get_valid_coin_change_dates() {
				$start_date = $this->date_minted;
				$clear = 1;
				$i = 0;
				foreach($this->schedule AS $k => $v) {
					if(strlen(key($v)) > 0 && key($v) != $_SESSION['user']['github_login']) {
						$clear = 0;
					}
					if($k == $this->today) {
						$clear = 0;
					}
					if($clear) {
						$dates[$i] = $k;
					}
					$i++;
				}

		return($dates);

	}
	public function kick_start_date($offset){
		$this->load_coin_owners($this->repo_name);
		$ok_dates = $this->get_valid_coin_change_dates();
		if(array_key_exists($offset, $ok_dates)) { 
			query("UPDATE asset SET date_created = DATE_ADD(date_created, INTERVAL $offset DAY) WHERE name = '".escape($this->repo_name)."'");	
			$this->date_minted = query_grab("SELECT date_created FROM asset WHERE name = '".escape($this->repo_name)."'");	
			$this->load_coin_owners($this->repo_name);
		} else { 
			return(0);
		}
	}
	public function load_coin_owners($repo) {
		$df = "%a, %b %e";
		$asset = query_assoc("SELECT a.*, w.deposit_amount FROM asset a LEFT JOIN wallet w ON a.name = w.repo  WHERE a.name = '".escape($repo)."'");
		$this->date_minted = $asset['date_created'];
		$this->asset_hash = $asset['transaction_hash'];
		$this->desposit_amount = $asset['deposit_amount'];	
		$q = query_load("SELECT * FROM commit_log WHERE repo = '".escape($repo)."'");
			
		for($i = 0; $i <=29; $i++) {
			$day = strftime($df, strtotime($this->date_minted) + $i*60*60*24 );
			$cal[$day] = array();
		}
		foreach($q AS $k => $v) {
			$day = strftime($df, strtotime($v['commit_date']));
			if(array_key_exists($day, $cal)) {
				$cal[$day][$v['username']]++;
			}
			$contributors[$v['username']]++;
		}
		foreach($cal AS $k => $v) {
			if(count($v) >0) {
				$tot += 1;
			}
			foreach($v AS $k2=>$v2) {
				$share[$k2] += (1 / count($v));	
			}
			
		}
		if($tot >0) {
		foreach($share AS $k => $v) {
			$share[$k] = round(100* $v / $tot, 1);
		}
		}
		$this->today = strftime($df, time());
		$this->share = $share;
		$this->contributors = $contributors;
		$this->schedule = $cal;
		$this->asset_id = $asset['asset_id'];
	}
	public function store_auth_details($token) {
		if(!$_SESSION['user']['email']) {
			$this->get_email($token);	
		}
	}
	public function get_featured_projects() {
		$q = query("SELECT a.*, r.* FROM asset a LEFT JOIN repo r ON a.name = r.name   ORDER BY date_created DESC LIMIT 4");
		while($r = fa($q)) {
			$r['contributors'] = query_count("SELECT DISTINCT user_id FROM commit_log WHERE repo_id = '".$r['repo_id']."'");
			$arr[] = $r;
			
		}
		return($arr);
	}
	protected function update_user($token) {
		$this->token = $token;
		$url = "https://api.github.com/user";
		$this->api_call($url);
		
		$user_array = array(	'token' => $token,
					'github_id' => $this->json_response->id,
					'github_login' => $this->json_response->login,
					'avatar_url' => $this->json_response->avatar_url,
					'public_repos' => $this->json_response->public_repos,
					'total_private_repos' => $this->json_response->total_private_repos,
					'owned_private_repos' => $this->json_response->owned_private_repos,
					'followers' => $this->json_response->followers,
					'following' => $this->json_response->following
					);

		$url = "https://api.github.com/user/emails";
		$this->api_call($url);
		if($this->json_response[0]->email) {
			$user_array['email'] = $this->json_response[0]->email;
		}
		if(count($this->json_response) > 0) {
			foreach($this->json_response AS $k => $v) {
				$emails[] = $v->email;
				if($v->primary) {
					$primary_email = $v->email;
				}
			}
		}
		$user_array['email'] = $primary_email;
		$user_array['all_emails'] = serialize($emails);
		$this->store_user($user_array);
	}
	protected function store_user($user_array) {
		if(is_numeric($user_array['github_id'])) {
			$exists = query_count("SELECT * FROM user WHERE github_id = '" . $user_array['github_id']  . "'");
			if($exists) {
				$q = "UPDATE user SET ";
				foreach($user_array AS $k => $v) {
					$q .= $k . " = '" . escape($v) . "', "; 
				}
				$q .= " last_update = NOW() WHERE github_id = '" . $user_array['github_id'] . "'";

				query($q);
			} else {
				$q = "INSERT INTO user(";
				foreach($user_array AS $k => $v) {
					$q .= $k . ', ';
				}
				$q .= "last_update) VALUES (";
				foreach($user_array AS $k => $v) {
					$q .= "'" . escape($v) . "', ";
				}
				$q .= "NOW())";
				query($q);
			}
		} else {
			die();
		}
		$_SESSION['user'] = $user_array;
		return(1);	
	}
	public function load_user() {
		if(!$_SESSION['token']) {
			$this->logout();
		}
		$this->token = $_SESSION['token'];
		if(!$_SESSION['user']) {
			$this->update_user($_SESSION['token']);
		}
		return(1);
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
	public function authenticate($code) {
		if($_SESSION['token']) {
			return(1);
		}
		$url = "https://github.com/login/oauth/access_token";
		$this->url = $url;
		$arr = array(
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'code' => $code,
			'redirect_uri' => SITE_URL.'/auth' 
			);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/json" 
			));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
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

		if($this->json_response->access_token) {
			$_SESSION['token'] = $this->json_response->access_token;
			$this->token = $_SESSION['token'];

			return(1);			
		}
		return(0);
	}
	public function logout() {
		unset($_SESSION['token']);
		unset($_SESSION['user']);
		header("Location: /");
		die();
	}
	protected function api_call($url) {
		$this->url = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.github.v3+json", 
			"Authorization: token ".$this->token 
			));
		
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
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
		if($this->json_response->message) {
			$this->error = $this->json_response->message;
			return 0;
		}
		return(1);	
	}
	public function reward_contributors() {
		
		$arr = json_decode($var);
		//echo "<PRE>";
		//print_r($arr);die();
		$q = query("SELECT * FROM repo LEFT JOIN user ON repo.user_id = user.github_id WHERE status = 4 AND TIMESTAMPDIFF(DAY, status_date, NOW()) <= 30");
		$sh = query("SELECT sha FROM commit_log");
		while($r = fa($sh)) {
			$sha[$r['sha']] = 1;
		}
		while($r = fa($q)) {
			$url = "https://api.github.com/repos/".$r['name']."/commits";
			$this->token = $r['token'];
			$this->api_call($url);
			$ret = $this->json_response;
			foreach($ret AS $k => $v) {
			  if(!array_key_exists($v->sha, $sha)) {
				$ins = "INSERT INTO commit_log(`repo`, `username`, `user_id`, `repo_id`, `commit_date`, `log_date`, `commit_url`, `sha`) 
				VALUES (
				'".escape($r['name'])."', '".escape($v->committer->login)."', '".escape($v->committer->id)."', '".$r['repo_id']."', '".escape($v->commit->committer->date)."', NOW(), '".escape($v->commit->url)."', '".escape($v->sha)."'
				)";
//				echo $ins."\n";
				query($ins);

			  }
			}
		}
		return(0);
	}
	public function load_repos() {
		$url = "https://api.github.com/user/repos";
		$this->api_call($url);
		$this->repos = $this->json_response;

		$sq = query("SELECT * FROM repo WHERE user_id = '".escape($_SESSION['user']['github_id'])."'");
		query("UPDATE wallet SET created = NOW() WHERE user_id='".escape($_SESSION['user']['github_id'])."'");
		while($r = fa($sq)) {
			$status[$r['name']] = $r;
		}
		foreach($this->repos AS $k => $v) {	
			if(array_key_exists($v->full_name, $status)) {
				$this->repos[$k]->status = $status[ $v->full_name ]['status'];
				$this->repos[$k]->address = $status[ $v->full_name ]['address'];
				if($status[ $v->full_name ]['status'] == 2) {
					$live_data = query_assoc("SELECT * FROM wallet WHERE bitcoin_address = '".escape($status[$v->full_name]['address'])."'");
					$this->repos[$k]->confirmations = $live_data['confirmations'];
					$this->repos[$k]->transaction_hash = $live_data['last_transaction_hash'];

				} elseif($status[ $v->full_name ]['status'] >= 3 ) {
					$hash = query_grab("SELECT transaction_hash FROM asset WHERE name = '".escape($v->full_name)."'");
					if(substr($hash,0,1) == '"' && substr($hash,-1,1) == '"') $hash = substr($hash,1,strlen($hash)-2);
					$this->repos[$k]->transaction_hash = $hash; 
				}
			} else {
				$this->repos[$k]->status = 0;
			}
		}
		return;
		//$user_url = $this->json_response->url;
		//$this->api_call($user_url);
	
		$repos_url = $this->json_response->repos_url;
		$this->api_call($repos_url);
		
		$contributors_url = $this->json_response[0]->url.'/collaborators';
		$this->api_call($contributors_url);


	}
	public function load_stored_repo($repo) {
		$this->repo_name = $repo;
		return(query_assoc("SELECT * FROM repo WHERE name = '".escape($repo)."'")); 
	}
	//Pulls from Github
	public function load_repo($repo) {
		$this->token = query_grab("SELECT token FROM repo r LEFT JOIN user u ON r.user_id = u.github_id WHERE r.name = '".escape($repo)."'");
		$this->hub = $repo;
		$url = "https://api.github.com/repos/" . $this->hub;
		
		$ret = $this->api_call($url);
			
		return($ret);		
	}
	public function get_contributors() {
		$url = "https://api.github.com/repos/" . $this->hub;
		$this->api_call($url);
		
		$contributors_url = $this->json_response->contributors_url;
		$this->api_call($contributors_url);

		//print_r($this->json_response);
	}
	
	protected function validate_hub($hub) {
		$e = explode("/", $hub);
		if(count($e) == 2) {
			$this->owner = $e[0];
			$this->project = $e[1];
			return 1;
		}
		return 0;
	}



}
?>
