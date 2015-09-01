<?php
require_once(MODULES_PATH.'/SendGrid.lib.php');
require_once(MODULES_PATH.'/SmtpApiHeader.lib.php');
require_once(MODULES_PATH.'/Swift-4.0.6/lib/swift_required.php');

class Email {

	public function send_scheduled_emails() {
		$q = query("SELECT * FROM email_hopper WHERE status = '1' ORDER BY rand() LIMIT 100");
		while($r = fa($q)) {
			$this->send_email($r['email_hopper_id']);
		}
	}
	public function queue_email($to, $from, $from_address, $subject, $html, $plaintext, $category = 'none') {
		$to = addslashes($to);
		$from = addslashes($from);
		$from_address = addslashes($from_address);
		$html = addslashes($html);
		$plaintext = addslashes($plaintext);
		$category = addslashes($category);
		$subject = addslashes($subject);
		query("INSERT INTO email_hopper (`to`, html, plaintext, `from`, from_address, subject, status, date_queued, category)
				VALUES
			('$to', '$html', '$plaintext', '$from', '$from_address', '$subject', '1', NOW(), '$category')");
		return(1);
	}
	public function send_email($email_id) {
		if(!is_numeric($email_id)) {
			return;
		}
		$email_data = query_assoc("SELECT * FROM email_hopper WHERE email_hopper_id = '$email_id' AND status = '1'");
		query("UPDATE email_hopper SET status = '2' WHERE email_hopper_id = '$email_id'");

	
		$to = $email_data['to'];
		if(query_count("SELECT * FROM do_not_email WHERE email LIKE '".addslashes($to)."'") > 0) {
			query("UPDATE email_hopper SET status = '-1' WHERE email_hopper_id = '$email_id'");
			return(0);
		}
		$h = new SmtpApiHeader();
		$h->addFilterSetting('subscriptiontrack', 'enable', 1);
		$h->addTo($to);
		$h->setUniqueArgs(array('category'=>$email_data['category'], $email_data['category'] => 1));
		$h->setCategory($email_data['category']);

		$subject = $email_data['subject'];
		$from = array($email_data['from_address'] => $email_data['from']);
		$text = $email_data['plaintext'];
		$html = $email_data['html'];

		$username = "gerrit@rezscore.com";
		$password = 're$ume$1';

		$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
		$transport ->setUsername($username);
		$transport ->setPassword($password);
		$swift = Swift_Mailer::newInstance($transport);

		$message = new Swift_Message($subject);
		$headers = $message->getHeaders();
		$headers->addTextHeader('X-SMTPAPI', $h->asJSON());

		$message->setFrom($from);
		$message->setBody($html, 'text/html');
		$message->setTo($to);
		$message->addPart($text, 'text/plain');
		
		if ($recipients = $swift->send($message, $failures))
		{
		  // This will let us know how many users received this message
		    // If we specify the names in the X-SMTPAPI header, then this will always be 1.  
//		      echo 'Message sent out to '.$recipients.' users';
			query("UPDATE email_hopper SET status = '3', date_sent = NOW() WHERE email_hopper_id = '$email_id'");
		      }
		      // something went wrong =(
		      else
		      {
			query("UPDATE email_hopper SET status = '3', date_sent = NOW(), notes = '".addslashes(print_r($failures,1))."' WHERE email_hopper_id = '$email_id'");
		        echo "Something went wrong - ";
			  print_r($failures);
			  }
	}


	
	
}

?>
