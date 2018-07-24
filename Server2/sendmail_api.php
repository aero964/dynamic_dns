<?php

/*
メール送信処理に使用するAPIです。
Server1からgetでbase64urlに変換されたメール内容が送信されてくることを想定しています。
*/

function base64url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

function base64url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
} 




if( $_GET["message"] && $_GET["to"] && $_GET["subject"] && $_GET["header"] ){
	$hantei = true;
}

if(!$hantei){
	die("null");
}

$message 	= base64url_decode($_GET["message"]);
$to	 		= $_GET["to"];
$subject	= base64url_decode($_GET["subject"]);
$header		= base64url_decode($_GET["header"]);

mb_language("Japanese"); 
mb_internal_encoding("UTF-8");

echo $to.$subject.$message.$header;


// 実行
mb_send_mail($to, $subject, $message, $header);
