<?php
//error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=stesalit user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
include('/var/www/html/pt/phpmailer/PHPMailerAutoload.php');


		//sendmail('tathagata.81@gmail.com','test');
$currentdate = date('Y-m-d');
$sqldevice = "select distinct(deviceid) from public.master_device_assign";
$querydevice = pg_query($sqldevice);
while($rsdevice = pg_fetch_array($querydevice))
{
		$deviceid = $rsdevice['deviceid'];
		$deviceid = $rsdevice['deviceid'];
		$sql_deice_details = "select b.deviceid,b.parent_id,b.user_id,b.apply_scheam from public.master_device_details as a
							LEFT JOIN public.master_device_assign as b on (a.id = b.deviceid)
							where a.id ='".$deviceid."' and b.group_id='2'";
		$query_deice_details = pg_query($sql_deice_details);
		if(pg_num_rows($query_deice_details) > 0)
		{
			$rs_deice_details = pg_fetch_array($query_deice_details);
			$schemaname = $rs_deice_details['apply_scheam'];
			$sql = "select * from ".$schemaname.".traker_device_alart_data where id not in (select alert_id from ".$schemaname.".generate_sms_mail where deviceid=".$deviceid.") and deviceid = ".$deviceid." and inserttime::date='".$currentdate."'";
			$query = pg_query($sql);
			while($rs = pg_fetch_array($query))
			{
				$parent_id = $rs['parent_id'];
				$user_id = $rs['user_id'];
				$deviceid = $rs['deviceid'];
				$group_id = 2;
				$alert_id = $rs['id'];
				$alert_description = $rs['description'];
				$alert_type = $rs['config_code'];
				$currentdate = $rs['currentdate'];
				$currentdate1 = date('d/m/Y',strtotime($rs['currentdate']));
				$currenttime = $rs['currenttime'];
				$latitude = $rs['latitude'];
				$longitude = $rs['longitude'];
				$geofenceid = $rs['geoenceid'];
				
				$sql_mail_sms_notify = "select * from ".$schemaname.".master_device_alart_conf where alart_code='".$alert_type."' and device_id='".$deviceid."' and active='1'";
				$query_mail_sms_notify = pg_query($sql_mail_sms_notify);
				$rs_mail_sms_notify = pg_fetch_array($query_mail_sms_notify);
				$email_notify = $rs_mail_sms_notify['isemailnotify'];
				$sms_notify = $rs_mail_sms_notify['isphonenotify'];
				if($alert_type != '3' || $alert_type != '4' || $alert_type != '6')
				{
					if($geofenceid != '')
					{
						$sql_last_alert = "select currentdate,currenttime from ".$schemaname.".generate_sms_mail where deviceid=".$deviceid." and alert_type=".$alert_type." and geoenceid=".$geofenceid." order by currentdate desc,currenttime desc limit 1";
					}
					else
					{
						$sql_last_alert = "select currentdate,currenttime from ".$schemaname.".generate_sms_mail where deviceid=".$deviceid." and alert_type=".$alert_type." order by currentdate desc,currenttime desc limit 1";
					}
				}
				else
				{
					$sql_last_alert = "select currentdate,currenttime from ".$schemaname.".generate_sms_mail where deviceid=".$deviceid." and alert_type=".$alert_type." order by currentdate desc,currenttime desc limit 1";
				}
				$query_last_alert = pg_query($sql_last_alert);
				$rs_last_alert = pg_fetch_array($query_last_alert);
				if(pg_num_rows($query_last_alert) > 0)
				{
					$last_alert_date = $rs_last_alert['currentdate'];
					$last_alert_time = $rs_last_alert['currenttime'];
					
					$timediff = "  SELECT DATE_PART('day','".$currentdate." ".$currenttime."'::timestamp - '".$last_alert_date." ".$last_alert_time."'::timestamp)*24*3600+DATE_PART('hours','".$currentdate." ".$currenttime."'::timestamp - '".$last_alert_date." ".$last_alert_time."'::timestamp)*3600+DATE_PART('minutes','".$currentdate." ".$currenttime."'::timestamp - '".$last_alert_date." ".$last_alert_time."'::timestamp)*60+DATE_PART('minutes','".$currentdate." ".$currenttime."'::timestamp - '".$last_alert_date." ".$last_alert_time."'::timestamp) as sec";
					$query_timediff = pg_query($timediff);
					$rs_timediff = pg_fetch_array($query_timediff);
					$time_difference = $rs_timediff['sec'];
					if($time_difference > 3600)
					{ 
						$sql_email_sms = "select a.alertemails,a.alertphnumbers,b.serial_no,a.device_name from ".$schemaname.".master_device_setup as a
										  LEFT JOIN public.master_device_details as b on (a.deviceid = b.id)
										  where a.deviceid = ".$deviceid."";
						$query_email_sms = pg_query($sql_email_sms);
						$rs_email_sms = pg_fetch_array($query_email_sms);
						$sms_number = $rs_email_sms['alertphnumbers'];
						$email = $rs_email_sms['alertemails'];
						$serial_no = $rs_email_sms['serial_no'];
						$device_name = $rs_email_sms['device_name'];
						
						$sql_user = "select firstname,lastname from public.user_login where id = ".$user_id."";
						$query_user = pg_query($sql_user);
						$rs_user = pg_fetch_array($query_user);
						$name = $rs_user['firstname']." ".$rs_user['lastname'];
						
						$sql_token = "select token_id from public.user_token_app where active=1 and closetime is null and user_id=".$user_id."";
						$query_token = pg_query($sql_token);
						$rs_token = pg_fetch_array($query_token);
						$token = $rs_token['token_id'];
							
						$firstname = $rs_email_sms['firstname'];
						$lastname = $rs_email_sms['lastname'];
						if(($rs['config_code'] == '4') || ($rs['config_code'] == '3'))
						{
							$sql_object = "select geoname from ".$schemaname.".master_geofence where id = ".$rs['geoanceid']."";
							$query_object = pg_query($sql_object);
							$rs_object = pg_fetch_array($query_object);
							$object_name = $rs_object['geoname'];
						} 
						$url = "https://www.google.com/maps/search/?api=1&query=$latitude,$longitude";
						$new_url = get_tiny_url($url);
						$sms_text = "9trax:Alert From:\nDevice:".$serial_no."\nType:".$rs['description']."\nDateTime:".$currentdate1." ".$currenttime."\nLink:$new_url";
						$email_text = "Hi,<br>
										 Information From 9trax<br>
										 Deviceid : ".$serial_no."<br>
										 Alias Name : ".$device_name."<br>
										 User Name : ".$name."<br>
										 Alert Type : ".$rs['description']."<br>
										 Date Time : $currentdate1 $currenttime <br>
										 Location :  $latitude $longitude <br>
										 Link :  $url <br>";
						if(($rs['config_code'] == '4') || ($rs['config_code'] == '3'))
						{
							$email_text = $email_text."			 
										   Remarks : Proximity Alert <br>
													 Reference Object - ".$object_name."<br>";
						}				
						$email_text = $email_text.
									 "Regards,<br>
									  9trax";
						$sms_number_arr = explode(',',$sms_number);
						if($sms_notify == '1')
						{
							for($i=0;$i<count($sms_number_arr);$i++)
							{
								/*$my_apikey = "0RMOYFJKSIBX7XFUB9FU"; 
								$destination = $sms_number_arr[$i];
								$message = $sms_text; 
								$api_url = "http://panel.apiwha.com/send_message.php"; 
								$api_url .= "?apikey=". urlencode ($my_apikey); 
								$api_url .= "&number=". urlencode ($destination); 
								$api_url .= "&text=". urlencode ($message); 
								$my_result_object = json_decode(file_get_contents($api_url, false)); */
								
								$mob_number = base64_encode($sms_number_arr[$i]);
								$text = base64_encode($sms_text);
								$post = array('mob_number' => $mob_number,'wahtsapp_text' => $text);
								$ch = curl_init('http://103.233.79.35/pt/cron/whatsapptest.php');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
								$response = curl_exec($ch);var_dump($response);
								curl_close($ch);								
							}
						}
						//echo $token;echo "hi876";exit;
						if($token != '')
						{
							$firebase_token = base64_encode($token);
							$message = base64_encode($rs['description']);
							$post = array('firebase_token' => $firebase_token,'message' => $message);
							$ch = curl_init('http://103.233.79.35/pt/cron/googlenotification.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$response = curl_exec($ch);var_dump($response);
							curl_close($ch);
						}
						if($email_notify == '1')
						{
							if($email != '')
							{
								$email_arr = explode(',',$email);
								for($i=0;$i<count($email_arr);$i++)
								{
									$email_address = trim($email_arr[$i]);
									$e = sendEmailNewSmtp('alert@9trax.com', $email_address, '', '', 'Alert Generate', $email_text, null, null);
								}
							}
						}
						//sendmail('tathagata.81@gmail.com',$email_text);
						$insert = "INSERT INTO ".$schemaname.".generate_sms_mail(parent_id, user_id, group_id, deviceid, alert_id, alert_description,alert_type, alert_text, email_text, sms_number, email, currentdate,currenttime,flag) VALUES (".$parent_id.", ".$user_id.", ".$group_id.", ".$deviceid.", ".$alert_id.", '".$alert_description."',".$alert_type.", '".$sms_text."', '".$email_text."', '".$sms_number."', '".$email."', '".$currentdate."','".$currenttime."','true') RETURNING id";
						$insert_query = pg_query($insert);
					 }
					}
					else
					{
						
						$sql_email_sms = "select a.alertemails,a.alertphnumbers,b.serial_no,a.device_name from ".$schemaname.".master_device_setup as a
										  LEFT JOIN public.master_device_details as b on (a.deviceid = b.id)
										  where a.deviceid = ".$deviceid."";
						$query_email_sms = pg_query($sql_email_sms);
						$rs_email_sms = pg_fetch_array($query_email_sms);
						$sms_number = $rs_email_sms['alertphnumbers'];
						$email = $rs_email_sms['alertemails'];
						$serial_no = $rs_email_sms['serial_no'];
						$device_name = $rs_email_sms['device_name'];
						
						$sql_user = "select firstname,lastname from public.user_login where id = ".$user_id."";
						$query_user = pg_query($sql_user);
						$rs_user = pg_fetch_array($query_user);
						$name = $rs_user['firstname']." ".$rs_user['lastname'];
						
						$sql_token = "select token_id from public.user_token_app where active=1 and closetime is null and user_id=".$user_id."";
						$query_token = pg_query($sql_token);
						$rs_token = pg_fetch_array($query_token);
						$token = $rs_token['token_id'];
							
						$firstname = $rs_email_sms['firstname'];
						$lastname = $rs_email_sms['lastname'];
						if(($rs['config_code'] == '4') || ($rs['config_code'] == '3'))
						{
							$sql_object = "select geoname from ".$schemaname.".master_geofence where id = ".$rs['geoanceid']."";
							$query_object = pg_query($sql_object);
							$rs_object = pg_fetch_array($query_object);
							$object_name = $rs_object['geoname'];
						} 
						$url = "https://www.google.com/maps/search/?api=1&query=$latitude,$longitude";
						$new_url = get_tiny_url($url);
						$sms_text = "9trax:Alert From:\nDevice:".$serial_no."\nType:".$rs['description']."\nDateTime:".$currentdate1." ".$currenttime."\nLink:$new_url";
						$email_text = "Hi,<br>
										 Information From 9trax<br>
										 Deviceid : ".$serial_no."<br>
										 Alias Name : ".$device_name."<br>
										 User Name : ".$name."<br>
										 Alert Type : ".$rs['description']."<br>
										 Date Time : $currentdate1 $currenttime <br>
										 Location :  $latitude $longitude <br>
										 Link :  $url <br>";
						if(($rs['config_code'] == '4') || ($rs['config_code'] == '3'))
						{
							$email_text = $email_text."			 
										   Remarks : Proximity Alert <br>
													 Reference Object - ".$object_name."<br>";
						}				
						$email_text = $email_text.
									 "Regards,<br>
									  9trax";
						$sms_number_arr = explode(',',$sms_number);
						if($sms_notify == '1')
						{
							for($i=0;$i<count($sms_number_arr);$i++)
							{
								$mob_number = base64_encode($sms_number_arr[$i]);
								$text = base64_encode($sms_text);
								$post = array('mob_number' => $mob_number,'wahtsapp_text' => $text);
								$ch = curl_init('http://103.233.79.35/pt/cron/whatsapptest.php');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
								$response = curl_exec($ch);var_dump($response);
								curl_close($ch);
								
							}
						}
						if($token != '')
						{
							$firebase_token = base64_encode($token);
							$message = base64_encode($rs['description']);
							$title = base64_encode("Alert Generate");
							$post = array('firebase_token' => $firebase_token,'message' => $message,'title' => $title);
							$ch = curl_init('http://103.233.79.35/pt/cron/googlenotification.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$response = curl_exec($ch);var_dump($response);
							curl_close($ch);
						}
						if($email_notify == '1')
						{
							if($email != '')
							{
								$email_arr = explode(',',$email);
								for($i=0;$i<count($email_arr);$i++)
								{
									$email_address = trim($email_arr[$i]);
									$e = sendEmailNewSmtp('alert@9trax.com', $email_address, '', '', 'Alert Generate', $email_text, null, null);
								}
							}
						}
						//sendmail('tathagata.81@gmail.com',$email_text);
						$insert = "INSERT INTO ".$schemaname.".generate_sms_mail(parent_id, user_id, group_id, deviceid, alert_id, alert_description,alert_type, alert_text, email_text, sms_number, email, currentdate,currenttime,flag) VALUES (".$parent_id.", ".$user_id.", ".$group_id.", ".$deviceid.", ".$alert_id.", '".$alert_description."',".$alert_type.", '".$sms_text."', '".$email_text."', '".$sms_number."', '".$email."', '".$currentdate."','".$currenttime."','true') RETURNING id";
						$insert_query = pg_query($insert);
					
					}
			}
		}
}
function get_tiny_url($url)  {  
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch);  
	return $data;  
}
function sendwhatsapp($number,$sms_text)
{
	$number = "919433236654";
	$data = array('phone' => $number,'body' => $sms_text);
	$json = json_encode($data); // Encode data to JSON
	// URL for request POST /message
	$url = 'https://eu7.chat-api.com/instance5726/message?token=ec0i5ib8er2slssd';
	// Make a POST request
	/*$post_data = array('method'  => 'POST','header'  => 'Content-type: application/json','content' => $json);
	$post_params = array('http' =>$post_data);
	$options = stream_context_create($post_params);
	$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);*/
	// Send a request
	echo $json;
	$options = stream_context_create(array(
		  'http'=>array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $json
		  )
		));
	$result = file_get_contents($url, false, $options);
	var_dump($result);
	exit;
}
function sendsms($number,$smsmessage) 
{
	$HOSTNAME = "http://anonymouse.org/cgi-bin/anon-www.cgi/http://mobicomm.dove-sms.com/submitsms.jsp";
	$USERNAME = "Stesalit";
	$PASSWORD = "754qwh";
	$SENDER = "CBITSS";
	$SMS_KEY = "d36862f224XX";
	$url = $HOSTNAME;
	$post_data = "user=".$USERNAME."&key=".$SMS_KEY."&mobile=".$number."&message=".$smsmessage."&senderid=".$SENDER."&accusage=1";
	ob_start();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_POST, TRUE);   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$response = curl_exec($ch);
	curl_close($ch);
	//$res =ob_get_contents();
	ob_end_clean();
	return $response;      
}
function sendmail1($email,$email_text)
{
	$to = $email;
	$subject = "Alert Generatet";
	$txt = $email_text;
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: <9trax.stesalit@gmail.com>' . "\r\n";
	if(mail($to,$subject,$txt,$headers))
	{
		echo 'sent';
	}
	else
	{
		echo 'not send';
	}
}
function sendmail($email,$email_text)
{
	//Create a new PHPMailer instance
	//$mail = new PHPMailer;
	/*$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = "localhost";
	$mail->SMTPDebug  = 0; */
	$mail = new PHPMailer(); // create a new object
	$mail->IsSMTP(true); // enable SMTP
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
	$mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
	$mail->Debugoutput = 'html';
	$mail->SMTPAuth = true; // authentication enabled
	$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
	//$mail->Host = "mail.9trax.com";
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; // or 587
	$mail->IsHTML(true);
	$mail->Username = "9trax.stesalit@gmail.com";
	$mail->Password = "Sil@12345";
	$mail->From = '9trax.stesalit@gmail.com';
	$mail->FromName = '9trax';
	//$mail->setFrom('admin@9trax.com', 'admin@9trax.com');
	$mail->setFrom('9trax.stesalit@gmail.com', '9trax');
	//$mail->addReplyTo('9trax.stesalit@gmail.com', '9trax');
	//$mail->AddCC('9trax.stesalit@gmail.com', '9trax');
	//$mail->addReplyTo('admin@9trax.com', '9trax');
	//$mail->AddCC('varun.stesalit@gmail.com ', 'Varun Komuroju');
	//$mail->AddCC('prasenjit13.gupta@gmail.com', 'Prasenjit Gupta');
    //$mail->AddCC('krishnendu.stesalit@gmail.com', 'Krishnendu Das Gupta');
	//$mail->AddCC('anirbanbasu.stesalit@gmail.com', 'Anirban Basu');
	//$mail->AddCC('arindam.stesalit@gmail.com', 'Arindam Kumar');
	//$mail->AddCC('subhadip.stesalit@gmail.com', 'Subhadip Das Mahapatra');
	//$mail->AddCC('tathagata.lm@stesalitsystems.com', 'Tathagata Lahiri Majumder');
	
	$mail->addAddress($email);
	$mail->Subject = 'Alert Generate';
	$mail->CharSet = 'UTF-8';
	$mail->msgHTML($email_text);
	$mail->AltBody = 'This is a plain-text message body';
	//send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}	
}
function sendEmailNewSmtp($from, $to, $cc, $bcc, $subject, $data, $file1=null, $file2=null) {
	$mail = new PHPMailer(); // create a new object
	$mail->IsSMTP(true);
	$mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
	$mail->Debugoutput = 'html';
	$mail->SMTPAuth = true; // authentication enabled
	$mail->Host = "mail.9trax.com";
	$mail->Port = 25; // or 587
	$mail->Username = "alert@9trax.com";
	$mail->Password = "Sil@45123";
	$mail->IsHTML(true);
	$mail->From = $from;
	$mail->FromName = '9trax';
	$mail->addAddress($to);
	$mail->Subject = $subject;
	$mail->CharSet = 'UTF-8';
	$mail->msgHTML($data);
	$mail->SMTPDebug  = 1;
	$mail->AltBody = 'This is a plain-text message body';
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}	
}
?>