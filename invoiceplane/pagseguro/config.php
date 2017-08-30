<?php

define("GATEWAY_MAIL", ""); //PagSeguro user mail
define("GATEWAY_TOKEN", "");
define("GATEWAY_URL_CHECKOUT", "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout");
//define("GATEWAY_URL_CHECKOUT", "https://ws.pagseguro.uol.com.br/v2/checkout");
define("GATEWAY_URL_PAYMENT", "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html");
//define("GATEWAY_URL_PAYMENT", "https://pagseguro.uol.com.br/v2/checkout/payment.html");
define("GATEWAY_URL_NOTIFICATIONS", "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications");
//define("GATEWAY_URL_NOTIFICATIONS", "https://ws.pagseguro.uol.com.br/v3/transactions/notifications");

define("GATEWAY_CURRENCY", "BRL"); //eg: BRL
define("IP_PAYMENT_METHOD_ID", "3"); //eg: PagSeguro

//Data Base
define("IP_DB_LOCAL", "localhost");
define("IP_DB_USER", "");
define("IP_DB_NAME", "");
define("IP_DB_PASS", "");

//Data Base Connection
$IP_DB_CONN = new PDO("mysql:host=". IP_DB_LOCAL .";dbname=". IP_DB_NAME .";charset=UTF8", IP_DB_USER, IP_DB_PASS);
$IP_DB_CONN->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>