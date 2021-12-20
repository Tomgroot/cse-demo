<?php

require_once('vendor/autoload.php');
require_once('config.php');

use Omnipay\Omnipay;
use Omnipay\Paynl\Message\Request\AuthenticateRequest;

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

    //TODO replace
    $data = ['amount' => 1, 'clientIp' => '84.247.45.3', 'returnUrl' => 'localhost'];

    $authenticate = $gateway->authenticate($data);
    if (!$authenticate instanceof AuthenticateRequest){
        throw new Exception('Authentication not initiated');
    }

    if (isset($_POST['transaction_id'])) {
        $authenticate->setTransactionId($_POST['transaction_id']);
    } else {
        $authenticate->setServiceId(SERVICEID)
            ->setDescription('Lorem Ipsum')
            ->setCurrency('EUR');
    }
    $cse = [
        'identifier'    => $payload['identifier'],
        'data'          => $payload['data']
    ];
    $authenticate->setCse($cse);

    if (isset($_POST['threeds_transaction_id'])) {
        $authenticate->setPayTdsAcquirerId(134)
            ->setPayTdsTransactionId($_POST['threeds_transaction_id']);
    }

    $authenticate->setTestMode(true);

    $result = $authenticate->send();

} catch (Exception $e) {
    $result = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result->getData());
