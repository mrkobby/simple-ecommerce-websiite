<?php
if ($_SERVER['REQUEST_METHOD'] != "POST") die ("No Post Variables");
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}
$url = "https://www.paypal.com/cgi-bin/webscr";
$curl_result=$curl_err='';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
curl_setopt($ch, CURLOPT_HEADER , 0);   
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$curl_result = @curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

$req = str_replace("&", "\n", $req);

if (strpos($curl_result, "VERIFIED") !== false) {
    $req .= "\n\nPaypal Verified OK";
} else {
	$req .= "\n\nData NOT verified from Paypal!";
	mail("mtckobby@gmail.com", "IPN interaction not verified", "$req", "From: mtckobby@gmail.com" );
	exit();
}

$receiver_email = $_POST['receiver_email'];
if ($receiver_email != "you@youremail.com") {
	$message = "Investigate why and how receiver email is wrong. Email = " . $_POST['receiver_email'] . "\n\n\n$req";
    mail("mtckobby@gmail.com", "Receiver Email is incorrect", $message, "From: mtckobby@gmail.com" );
    exit(); 
}
if ($_POST['payment_status'] != "Completed") {
	echo "Transaction complete";
}
require_once 'connect_to_mysql.php';
$this_txn = $_POST['txn_id'];
$sql = mysql_query("SELECT id FROM transactions WHERE txn_id='$this_txn' LIMIT 1");
$numRows = mysql_num_rows($sql);
if ($numRows > 0) {
    $message = "Duplicate transaction ID occured so we killed the IPN script. \n\n\n$req";
    mail("mtckobby@gmail.com", "Duplicate txn_id in the IPN system", $message, "From: mtckobby@gmail.com" );
    exit(); 
} 
$product_id_string = $_POST['custom'];
$product_id_string = rtrim($product_id_string, ","); 
 
$id_str_array = explode(",", $product_id_string);  
$fullAmount = 0;
foreach ($id_str_array as $key => $value) {
    
	$id_quantity_pair = explode("-", $value); 
	$product_id = $id_quantity_pair[0]; 
	$product_quantity = $id_quantity_pair[1]; 
	$sql = mysql_query("SELECT price FROM products WHERE id='$product_id' LIMIT 1");
    while($row = mysql_fetch_array($sql)){
		$product_price = $row["price"];
	}
	$product_price = $product_price * $product_quantity;
	$fullAmount = $fullAmount + $product_price;
}
$fullAmount = number_format($fullAmount, 2);
$grossAmount = $_POST['mc_gross']; 
if ($fullAmount != $grossAmount) {
        $message = "Possible Price Jack: " . $_POST['payment_gross'] . " != $fullAmount \n\n\n$req";
        mail("mtckobby@gmail.com", "Price Jack or Bad Programming", $message, "From: mtckobby@gmail.com" );
        exit(); 
} 
$txn_id = $_POST['txn_id'];
$payer_email = $_POST['payer_email'];
$custom = $_POST['custom'];
 
$sql = mysql_query("INSERT INTO transactions (product_id_array, payer_email, first_name, last_name, payment_date, mc_gross, payment_currency, txn_id, receiver_email, payment_type, payment_status, txn_type, payer_status, address_street, address_city, address_state, address_zip, address_country, address_status, notify_version, verify_sign, payer_id, mc_currency, mc_fee) 
   VALUES('$custom','$payer_email','$first_name','$last_name','$payment_date','$mc_gross','$payment_currency','$txn_id','$receiver_email','$payment_type','$payment_status','$txn_type','$payer_status','$address_street','$address_city','$address_state','$address_zip','$address_country','$address_status','$notify_version','$verify_sign','$payer_id','$mc_currency','$mc_fee')") or die ("unable to execute the query");

mysql_close();
 
mail("mtckobby@gmail.com", "NORMAL IPN RESULT YAY MONEY!", $req, "From: mtckobby@gmail.com");
?>