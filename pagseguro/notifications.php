<?php
include_once("config.php");
$code = $_POST['notificationCode'];
$date = date('Y-m-d H:i:s');
$date2 = date('Y-m-d');

if ($environment == "sandbox"){
    $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/$code?email=$email&token=$tokenSandbox";
} elseif ($environment == "production"){
    $url = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/$code?email=$email&token=$tokenProduction";
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: 504af853-3a0b-26ed-7ea6-de08b85b7bee"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $xml= simplexml_load_string($response);
    $status = $xml->status;
    $payMet = $xml->paymentMethod->type;
    $id = $xml->items->item->id;
    $paid = $xml->items->item->amount;
    $payMetArray = ["", "Cartao de credito", "Debito online (TEF)", "Saldo PagSeguro", "Oi Paggo", "Deposito em conta"];
    $payMetResult = $payMetArray["$payMet"];

    if ($status == 3) {
       try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE ip_invoices SET invoice_status_id='4', is_read_only='1', invoice_date_modified='$date' WHERE invoice_id='$id'";
            // Prepare statement
            $stmt = $conn->prepare($sql);
            // execute the query
            $stmt->execute();
            // echo a message to say the UPDATE succeeded
            echo $stmt->rowCount() . " records UPDATED successfully";

            $sql2 = "UPDATE ip_invoice_amounts SET invoice_balance='0', invoice_paid='$paid' WHERE invoice_id='$id'";
            // Prepare statement
            $stmt2 = $conn->prepare($sql2);
            // execute the query
            $stmt2->execute();
            // echo a message to say the UPDATE succeeded
            echo $stmt2->rowCount() . " records UPDATED successfully";

            $sql3 = "INSERT INTO ip_payments (invoice_id, payment_method_id, payment_date, payment_amount, payment_note)
            VALUES ('$id', '$payment_method_id', '$date2', '$paid', '$payMetResult')";
            // use exec() because no results are returned
            $conn->exec($sql3);
            }
        catch(PDOException $e)
            {
            echo $sql . "<br>" . $e->getMessage();
            }

        $conn = null;
    } else {
        echo "Status: " .$status;
    }

}

?>
