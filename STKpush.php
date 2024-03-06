<?php

// Replace these variables with your actual credentials
$consumerKey = 't5ZGoPXN33y3A9KJHcULutzGjzXEswJiehqmLAt0MToU67SI';
$consumerSecret = 'nu87lQHSRCNAZ4qE86mtqT3uVuaq7S1LcAExADeKHxdCk2ZxzPGIeWt2dDbl49Yi';
$shortcode = "174379";
$lipaNaMpesaOnlinePasskey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$phonenumber = '254759419486';
$basicAuthToken = base64_encode($consumerKey.':'.$consumerSecret);

// Generate the timestamp
$timestamp = date('YmdHis');

// Generate the password
$password = base64_encode($shortcode.$lipaNaMpesaOnlinePasskey.$timestamp);

function getValidatedAccessToken() {
    global $basicAuthToken;
    // Prepare the headers
    // $headers = array(
    //     'Authorization: "Bearer ' .$basicAuthToken.'"'
    // );
    // print_r($headers);
    
    // Make the GET request
    $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic dDVaR29QWE4zM3kzQTlLSkhjVUx1dHpHanpYRXN3SmllaHFtTEF0ME1Ub1U2N1NJOm51ODdsUUhTUkNOQVo0cUU4Nm10cVQzdVZ1YXE3UzFMY0FFeEFEZUtIeGRDazJaeHpQR0llV3QyZERibDQ5WWk=']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo $response;
        return '';
    }
    
    $data = json_decode($response, true);
    return $data['access_token'];
}

// Define the endpoint
$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

// Prepare the request body
$body = array(
    'BusinessShortCode'=>$shortcode,
    'Password'=>$password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => 1,
    'PartyA'=> $phonenumber,
    'PartyB'=> $shortcode,
    'PhoneNumber'=> $phonenumber,
    'CallBackURL' =>'https://e028-41-212-117-244.ngrok-free.app/Grocery/',
    'AccountReference' =>'GroceryApp',
    'TransactionDesc' => 'check out'
);


// Set up cURL to make the request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: "Bearer '.getValidatedAccessToken().'"'
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    die(curl_error($ch));
}

// Close cURL
curl_close($ch);

// Output the response
echo $response;

?>
