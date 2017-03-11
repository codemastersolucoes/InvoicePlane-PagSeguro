<?php

require "config.php";

$itemId1 = $_POST['id'];
$itemDescription1 = rawurlencode(APP_CORPORATION ." ". $itemId1);
$itemAmount1 = $_POST['price'];
$itemQuantity1 = "1";

if ( APP_PAYMENT_METHOD_ID == "sandbox" ) {

    $endpoint = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/?email=". APP_MAIL ."&token=". APP_TOKEN_SANDBOX ."&currency=". APP_CURRENCY ."&itemId1=$itemId1&itemDescription1=$itemDescription1&itemAmount1=$itemAmount1&itemQuantity1=$itemQuantity1";

} elseif ( APP_PAYMENT_METHOD_ID == "production" ) {

    $endpoint = "https://ws.pagseguro.uol.com.br/v2/checkout/?email=". APP_MAIL ."&token=". APP_TOKEN_PRODUCTION ."&currency=". APP_CURRENCY ."&itemId1=$itemId1&itemDescription1=$itemDescription1&itemAmount1=$itemAmount1&itemQuantity1=$itemQuantity1";

}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $endpoint,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded; charset=ISO-8859-1"
  ),
));

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);

if ( $error ) {
    echo "cURL Error #:". $error;
} else {
    $xml= simplexml_load_string($response);

    if ( APP_PAYMENT_METHOD_ID == "sandbox" ) {

        header("Location: https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=". $xml->code);

    } elseif ( APP_PAYMENT_METHOD_ID == "production" ){

        header("Location: https://pagseguro.uol.com.br/v2/checkout/payment.html?code=". $xml->code);

    }

}

?>
