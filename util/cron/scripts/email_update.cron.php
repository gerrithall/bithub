<?php
		require_once(MODULES_PATH.'/Email.lib.php');
		require_once(MODULES_PATH.'/Github.lib.php');
		$G = new Github;
		$feat = $G->get_featured_projects();
		$q = query("SELECT u.user_id, u.email, c.username, count(*) FROM user u LEFT JOIN commit_log c ON u.github_id = c.user_id GROUP BY u.user_id;");
		while($r = fa($q)) {
			if($r['username']) {
				$coins = array();
				$projectq = query("SELECT DISTINCT(repo) FROM commit_log WHERE username = '".escape($r['username'])."'");
				while($r1 = fa($projectq)) {
					$G1 = new Github;
					$G1->load_coin_owners($r1['repo']);
					$share = $G1->share;
					if(array_key_exists($r['username'], $share)) {
						$coins[$r1['repo']] = $share[$r['username']];
					}
				}

				$E = new Email;
				$vals = array('coins' => $coins, 'proj' => $feat);
				$html = $E->load_email("coins", $vals);
				$txt = $E->load_email("coins", $vals,1);
				$E->queue_email($r['email'], 'Bithub', 'hellyes@gobithub.com', 'Bithub Update', $html, $txt, 'status');
			} else {
				$E = new Email;
				$vals = array( 'proj' => $feat);
				$html = $E->load_email("no-coins", $vals);
				$txt = $E->load_email("no-coins", $vals,1);
				$E->queue_email($r['email'], 'Bithub', 'hellyes@gobithub.com', 'Bithub Update', $html, $txt, 'status');
			}
		}


?>
