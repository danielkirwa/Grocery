<?php
require_once('connection.php');

//YOU MPESA API KEYS
$consumerKey = "t5ZGoPXN33y3A9KJHcULutzGjzXEswJiehqmLAt0MToU67SI";
$consumerSecret = "nu87lQHSRCNAZ4qE86mtqT3uVuaq7S1LcAExADeKHxdCk2ZxzPGIeWt2dDbl49Yi"; 
//ACCESS TOKEN URL
$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$headers = ['Content-Type:application/json; charset=utf8'];
$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
echo $access_token = $result->access_token;
curl_close($curl);