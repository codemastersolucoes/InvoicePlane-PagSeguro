<?php

require "config.php";
$endpoint = GATEWAY_URL_CHECKOUT ."/?email=". GATEWAY_MAIL ."&token=". GATEWAY_TOKEN ."&currency=". GATEWAY_CURRENCY ."&itemId1=". $_REQUEST['number'] ."&itemDescription1=". rawurlencode($_REQUEST['name'] ." - Fatura ". $_REQUEST['number']) ."&itemAmount1=". $_REQUEST['balance'] ."&itemQuantity1=1";

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

if ( !$error ) {
  $xml= simplexml_load_string($response);
  header("Location: ". GATEWAY_URL_PAYMENT ."?code=". $xml->code);

} else {
  echo "Fail";

}

?>