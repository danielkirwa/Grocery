<?php

// Replace these values with your own
// Set your credentials
$consumer_key = 'Hcq54WrAnWnm5bWvJWuCdI0JCCvyiPCo';
$consumer_secret = 'wurmFd7Gt2DAp2ve';
$Business_Code = '174379';
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$Type_of_Transaction = 'CustomerPayBillOnline';
$Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$phone_number = $_POST['phone_number'];
$OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$total_amount = $_POST['amount'];
$CallBackURL = 'https://mydomain.com/path';
$Time_Stamp = date("Ymdhis");

// Get access token
$credentials = base64_encode($consumer_key . ':' . $consumer_secret);
$ch = curl_init($Token_URL);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$json = json_decode($result, true);
$access_token = $json['access_token'];

// Prepare STK push data
$data = array(
    'BusinessShortCode' => $Business_Code,
    'Password' => base64_encode($Business_Code . $Passkey . $Time_Stamp),
    'Timestamp' => $Time_Stamp,
    'TransactionType' => $Type_of_Transaction,
    'Amount' => $total_amount,
    'PartyA' => $phone_number,
    'PartyB' => $Business_Code,
    'PhoneNumber' => $phone_number,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => 'Test',
    'TransactionDesc' => 'Test Payment'
);

// Send STK push request
$ch = curl_init($OnlinePayment);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Output the response
echo $response;



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STK Push Form</title>
</head>
<body>
    <h2>STK Push Form</h2>
    <form id="stk-push-form" action="stk_push.php" method="POST">
        <label for="phone-number">Phone Number:</label><br>
        <input type="text" id="phone-number" name="phone-number" required><br><br>
        <label for="amount">Amount:</label><br>
        <input type="text" id="amount" name="amount" required><br><br>
        <button type="submit">Initiate STK Push</button>
    </form>
</body>
</html>
