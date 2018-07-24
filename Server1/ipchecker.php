<?php

/* 自分自身のグローバルIPアドレスを取得したいサーバに設置。
cronを使用して10分毎に動かす

OP25B規制を食らってserver1からメールを送信できないような環境を想定しています。 */

$docroot = "/path/to";

function base64url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

function base64url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
} 


date_default_timezone_set('Asia/Tokyo');

$present_ip 	= file_get_contents("http://server2/ipchecker/getmyip.php");

$old_ip		 	= file_get_contents("/path/to/ipchecker.txt");
$last_modified  = date("Y-m-d H:i:s", filemtime("/path/to/ipchecker.txt"));

sleep(1);

	file_put_contents("/path/to/ipchecker.txt", $present_ip);

	/* メール送信処理のために文字列を整形 */

	$message 	 =	"IPアドレスが変更になったようです。"										.PHP_EOL.PHP_EOL;
	$message 	.=	"古いIP　　: "								.$old_ip						.PHP_EOL;
	$message	.=  "新しいIP　: "								.$present_ip					.PHP_EOL;
	$message	.=	"確認日時　: "								.date("Y-m-d H:i:s")			.PHP_EOL;
	$message	.=	"最終更新　: "								.$last_modified					.PHP_EOL;

	$email		 =  "dummyaddress@example.com";
	$header 	 =	"From: $email\nReply-To: $email\n";
	$to 		 =  "reciever@example.com";

	$header 	 = base64url_encode($header);

if($present_ip != $old_ip){

	$message	 = base64url_encode($message);
	$subject	 = base64url_encode("【重要】IPアドレスが変わったので通知します\n");


	echo "<pre>".PHP_EOL;
	echo file_get_contents("http://server2/ipchecker/sendmail_api.php?message=$message&to=$to&subject=$subject&header=$header");
	echo "</pre>".PHP_EOL;

}else{

	$message	= base64url_encode(PHP_EOL."特に変更はないようです".PHP_EOL.PHP_EOL."現在のIP: ".$present_ip.PHP_EOL."確認日時: ".date('Y-m-d H:i:s').PHP_EOL);
	$subject	= base64url_encode("IPアドレスに変更はありませんでした\n");

	echo "<pre>".PHP_EOL;
	echo file_get_contents("http://server2/ipchecker/sendmail_api.php?message=$message&to=$to&subject=$subject&header=$header");
	echo "</pre>".PHP_EOL;
}

exit();
?>
