<?php
include_once("config.php");

$itemId1 = $_POST['id'];
$itemDescription1 = rawurlencode($corporation ." ". $itemId1);
$itemAmount1 = $_POST['price'];
$itemQuantity1 = "1";

if ($environment == "sandbox"){
    $url = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/?email=$email&token=$tokenSandbox&currency=$currency&itemId1=$itemId1&itemDescription1=$itemDescription1&itemAmount1=$itemAmount1&itemQuantity1=$itemQuantity1";
} elseif ($environment == "production"){
    $url = "https://ws.pagseguro.uol.com.br/v2/checkout/?email=$email&token=$tokenProduction&currency=$currency&itemId1=$itemId1&itemDescription1=$itemDescription1&itemAmount1=$itemAmount1&itemQuantity1=$itemQuantity1";
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
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
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $xml= simplexml_load_string($response);

    if ($environment == "sandbox"){
        header("Location: https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=".$xml->code."");
    } elseif ($environment == "production"){
        header("Location: https://pagseguro.uol.com.br/v2/checkout/payment.html?code=".$xml->code."");
    }

}

?>
