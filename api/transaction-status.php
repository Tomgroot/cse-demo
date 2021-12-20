<?php
require_once('../vendor/autoload.php');
require_once('../config.php');

use Omnipay\Omnipay;

try {
    if (!isset($_GET['transaction_id'])) {
        throw new Exception('Invalid transaction Id');
    }

    $gateway = Omnipay::create('Paynl');
    $gateway->setApiToken(APITOKEN);
    $gateway->setTokenCode(TOKENCODE);
    $gateway->setServiceId(SERVICEID);
    $fetch_authentication = $gateway->fetchAuthenticationStatus();
    $fetch_authentication->setTransactionId(filter_var($_GET['transaction_id'], FILTER_SANITIZE_STRING));
    $result = $fetch_authentication->send();
    $response = $result->getThreeDS();
    $response['result'] = $result->isSuccessful() ? "1" : "0";
} catch (Exception $e) {
    $response = array('result' => '0', 'errorMessage' => $e->getMessage());
}

header('content-type: application/json');
echo json_encode($response);