<?php
/*
 * Loanbase.lib.php
 * Manage BLC
 * 
 * copyright 2015 zcor
 */


class Loanbase {
	public function get_loan_investors($id) {
		$url = "https://api.bitlendingclub.com/api/investments/".$id;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$ret = curl_exec($ch);
		$a = json_decode($ret);
		return($a);
	}

	public function refresh_data() {
$q = query("SELECT * FROM loanbase ORDER BY score * 2 + rand()");

$z = 0;
while($r = fa($q)) {
  $z++;
  if($z <30) {
	$i = $this->get_loan_investors($r['loanbase_id']);
	$count = count($i->investments);
	$g = 0;
	$rate = 0;
	foreach($i->investments AS $k => $v) {
		if($v->investorId == 15716) {
			$g = 1;
		}
		$rate += $v->rate;
		$weighted_rate += $v->rate * $v->amount;
	}
	$avg = 0;
	if($count > 0) { $avg = $rate / $count; }
	query("UPDATE loanbase SET community_rate = '".($avg / 100)."', update_date = NOW(), investor_count = $count WHERE loanbase_id = '".$r['loanbase_id']."'");
	if($g) {
		query("UPDATE loanbase SET update_date = NOW(), investor_count = $count, g_amount = '".escape($v->amount)."', g_rate =  '".escape($v->rate)."', g_date =  '".escape($v->dateInvested)."' WHERE loanbase_id = '".$r['loanbase_id']."'");

	}
  }
}

	}
}
?>
