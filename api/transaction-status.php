<?php

require_once('vendor/autoload.php');
require_once('config.php');

try {
    if (!isset($_GET['transaction_id'])) {
        throw new Exception('Invalid transaction Id');
    }

    $gateway = Omnipay::create('Paynl');
    $fetch_transaction = $gateway->fetchTransaction();
    $fetch_transaction->setTransactionId(filter_var($_GET['transaction_id'], FILTER_SANITIZE_STRING));
    $result = $fetch_transaction->send();
} catch (Exception $e) {
    $result = array('result' => '0', 'errorMessage' => $e->getMessage());
}

header('content-type: application/json');
echo json_encode($result->getStatus());