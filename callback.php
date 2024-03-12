<?php
require_once('connection.php');

header("Content-Type: application/json");
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "w");
fwrite($log, $stkCallbackResponse);
fclose($log);

$data = json_decode($stkCallbackResponse);

// Check if JSON decoding was successful
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Error decoding JSON data']);
    exit;
}
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? '';
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? '';
$ResultCode = $data->Body->stkCallback->ResultCode ?? '';
$ResultDesc = $data->Body->stkCallback->ResultDesc ?? '';
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? '';
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? '';
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[3]->Value ?? '';

// $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
// $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
// $ResultCode = $data->Body->stkCallback->ResultCode;
// $ResultDesc = $data->Body->stkCallback->ResultDesc;
// $Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
// $TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
// $UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[3]->Value;
// //CHECK IF THE TRASACTION WAS SUCCESSFUL 
// if ($ResultCode == 0) {
//   //STORE THE TRANSACTION DETAILS IN THE DATABASE
//   $sql = "INSERT INTO transactions (MerchantRequestID,CheckoutRequestID,ResultCode,Amount,MpesaReceiptNumber,PhoneNumber)
//   VALUES ('$MerchantRequestID','$CheckoutRequestID','$ResultCode','$Amount','$TransactionId','$UserPhoneNumber')";
// //mysqli_query($database, "INSERT INTO transactions (MerchantRequestID,CheckoutRequestID,ResultCode,Amount,MpesaReceiptNumber,PhoneNumber) VALUES ('$MerchantRequestID','$CheckoutRequestID','$ResultCode','$Amount','$TransactionId','$UserPhoneNumber')");
// }

// Check if the transaction was successful
if ($ResultCode == 0) {
    // STORE THE TRANSACTION DETAILS IN THE DATABASE
    $insertQuery = "INSERT INTO transactions (MerchantRequestID,CheckoutRequestID,ResultCode,Amount,MpesaReceiptNumber,PhoneNumber) VALUES ('$MerchantRequestID','$CheckoutRequestID','$ResultCode','$Amount','$TransactionId','$UserPhoneNumber')";
    
    if (mysqli_query($con, $insertQuery)) {
        echo json_encode(['status' => 'success', 'message' => 'Transaction details stored successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error storing transaction details in the database: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Transaction not successful. ResultCode: ' . $ResultCode]);
}

?>