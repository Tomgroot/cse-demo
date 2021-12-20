<?php

require_once('../vendor/autoload.php');
require_once('../config.php');

use Omnipay\Omnipay;
use Omnipay\Paynl\Message\Request\AuthorizeRequest;

try {
    if (!isset($_POST['pay_encrypted_data'])) {
        throw new Exception('Missing payload');
    }

    $payload = json_decode($_POST['pay_encrypted_data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid json');
    }

    $gateway = Omnipay::create('Paynl');
    $gateway->setApiToken(APITOKEN);
    $gateway->setTokenCode(TOKENCODE);
    $gateway->setServiceId(SERVICEID);

    $data = ['amount' => AMOUNT, 'clientIp' => CLIENT_IP, 'returnUrl' => RETURN_URL];
    $authorize = $gateway->authorize($data);

    if (!$authorize instanceof AuthorizeRequest){
        throw new Exception('Authorization not initiated');
    }

    $authorize->setTransactionId($_POST['transaction_id']);

    $cse = [
        'identifier'    => $payload['identifier'],
        'data'          => $payload['data']
    ];
    $authorize->setCse($cse);

    $authorize->setPayTdsAcquirerId($_POST['acquirer_id'])
        ->setPayTdsTransactionId($_POST['threeds_transaction_id']);

    $authorize->setTestMode(true);
    $result = $authorize->send();
    if (!$result->isSuccessful()) {
        throw new Exception($result->getMessage());
    }
    $response = $result->getData();

} catch (Exception $e) {
    $response = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($response);