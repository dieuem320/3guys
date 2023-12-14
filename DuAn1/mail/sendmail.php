<?php
include('smtp/PHPMailerAutoload.php');


function smtp_mailer($to,$subject, $msg){
	$mail = new PHPMailer(); 
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'tls'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	//$mail->SMTPDebug = 2; 
	$mail->Username = "vandieukd@gmail.com"; //email gửi tin nhắn
	$mail->Password = "yqen kydw vkgw giis"; // app password
	$mail->SetFrom("vandieukd@gmail.com");
	$mail->Subject = $subject; // tiêu đề tin nhắn
	$mail->Body =$msg; // tin nhắn
	$mail->AddAddress($to);// email nhận tin nhắn
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if(!$mail->Send()){
		echo $mail->ErrorInfo;
	}else{
		return 'Sent';
	}
}
?>