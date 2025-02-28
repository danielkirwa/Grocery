<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values of the form fields
    $stktotal = $_POST["stktotal"];
    $txtmpesa = $_POST["txtmpesa"];
    


require_once('connection.php');

$NGROK_URL = 'https://ae27-41-89-10-241.ngrok-free.app';

//INCLUDE THE ACCESS TOKEN FILE
include 'accesstoken.php';
date_default_timezone_set('Africa/Nairobi');
$processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = $NGROK_URL . '/Grocery/callback.php';
//$callbackurl = 'https://juelgasolutions.co.tz/daraja/callback.php';
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
$BusinessShortCode = '174379';
$Timestamp = date('YmdHis');
// ENCRIPT  DATA TO GET PASSWORD
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
$phone = $txtmpesa;//phone number to receive the stk push
$money = $stktotal;
$PartyA = $phone;
$PartyB = '254708374149';
$AccountReference = 'Tumaini Groceries';
$TransactionDesc = 'Payment of groceries';
$Amount = $money;
$stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
//INITIATE CURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); //setting custom header
$curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $callbackurl,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
//ECHO  RESPONSE
$data = json_decode($curl_response);
$CheckoutRequestID = $data->CheckoutRequestID;
$ResponseCode = $data->ResponseCode;
if ($ResponseCode == "0") {
echo "The CheckoutRequestID for this transaction is : " . $CheckoutRequestID;
}
header("Location: grocery.php");
}else{
  echo "error setting totals and phone number";
}