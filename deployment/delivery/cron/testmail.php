<?php
include('/var/www/html/pt/phpmailer/PHPMailerAutoload.php');
$mail = new PHPMailer(); // create a new object
	$mail->IsSMTP(true);
	// debugging: 1 = errors and messages, 2 = messages only
	$mail->Debugoutput = 'html';
	$mail->SMTPAuth = true; // authentication enabled
	$mail->Host = "ssl://mail.9trax.com";
	$mail->SMTPSecure = "ssl"; 
	$mail->Port = 465; // or 587
	$mail->Username = "alert@9trax.com";
	$mail->Password = "Sil@45123";
	$mail->IsHTML(true);
	$mail->From = 'alert@9trax.com';
	$mail->FromName = '9trax';
	$mail->addAddress('tathagata.81@gmail.com');
	$mail->Subject = 'test mail';
	$mail->CharSet = 'UTF-8';
	$mail->msgHTML('test');
	$mail->SMTPDebug  = 1;
	$mail->AltBody = 'This is a plain-text message body';
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}	
?>