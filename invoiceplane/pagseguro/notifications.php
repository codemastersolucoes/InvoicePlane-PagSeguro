<?php

header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
require "config.php";
$endpoint = GATEWAY_URL_NOTIFICATIONS ."/". $_POST['notificationCode'] ."?email=". GATEWAY_MAIL ."&token=". GATEWAY_TOKEN;

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $endpoint,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded; charset=ISO-8859-1"
  ),
));
$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

if ( !$error ) {
  $xml= simplexml_load_string($response);
  $status = $xml->status;
  $payMet = $xml->paymentMethod->type;
  $id = $xml->items->item->id;
  $paid = $xml->items->item->amount;
  $payMetArray = ["", "Cartao de crédito", "Débito online (TEF)", "Saldo PagSeguro", "Oi Paggo", "Deposito em conta"];
  $payMetArray = $payMetArray["$payMet"];
  $payStatus = ["", "Aguardando pagamento", "Em análise", "Paga", "Disponível", "Em disputa", "Devolvida", "Cancelada", "Debitado", "Retenção temporária"];
  $payStatus = $payStatus["$status"];

  $select = $IP_DB_CONN->prepare("SELECT * FROM ip_invoices WHERE invoice_id LIKE :a");
  $select->bindValue( ":a", $id);
  $select->execute();
  foreach($select->fetchAll() as $k=>$v) {
    $invoiceStatusId = $v['invoice_status_id'];
  }

  if ( $select->rowCount() == 1 ) {
    if ( $invoiceStatusId != 4 AND $status == 3 ) {
      $update = $IP_DB_CONN->prepare("UPDATE ip_invoices SET invoice_status_id=:a, is_read_only=:b, invoice_date_modified=:c WHERE invoice_id=:d");
      $update->bindValue(":a", 4);
      $update->bindValue(":b", 1);
      $update->bindValue(":c", date('Y-m-d H:i:s'));
      $update->bindValue(":d", $id);
      $update->execute();

      $update = $IP_DB_CONN->prepare("UPDATE ip_invoice_amounts SET invoice_balance=:a, invoice_paid=:b WHERE invoice_id=:c");
      $update->bindValue(":a", 0);
      $update->bindValue(":b", $paid);
      $update->bindValue(":c", $id);
      $update->execute();

      $select = $IP_DB_CONN->prepare("SELECT * FROM ip_payments WHERE invoice_id LIKE :a");
      $select->bindValue( ":a", $id);
      $select->execute();
      foreach($select->fetchAll() as $k=>$v) {
        $invoiceId = $v['invoice_id'];
      }
      if ( $invoiceId == 0 ) {
        $insert = $IP_DB_CONN->prepare("INSERT INTO ip_payments (invoice_id, payment_method_id, payment_date, payment_amount, payment_note) VALUES (:a, :b, :c, :d, :e)");
        $insert->bindValue(":a", $id);
        $insert->bindValue(":b", IP_PAYMENT_METHOD_ID);
        $insert->bindValue(":c", date('Y-m-d'));
        $insert->bindValue(":d", $paid);
        $insert->bindValue(":e", $payMetArray);
        $insert->execute();

      }

    }
    $textLog = "date: ". date('Y-m-d H:i:s') .", itemId1: ". $id .", status: ". $payStatus .";\n";

  } else {
    $textLog = "date: ". date('Y-m-d H:i:s') .", itemId1: ". $id .", not found in database;\n";

  }

} else {
  $textLog = "date: ". date('Y-m-d H:i:s') .", notificationCode ". $_POST['notificationCode'] .", error: ". $error .";\n";

}

//log
$file = fopen("log.txt", "a");
fwrite($file, $textLog);
fclose($file);

?>
