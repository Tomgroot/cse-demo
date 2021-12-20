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

    $data = ['amount' => AMOUNT, 'clientIp' => CLIENT_IP, 'returnUrl' => RETURN_URL];
    $authenticate = $gateway->authenticate($data);

    if (!$authenticate instanceof AuthenticateRequest){
        throw new Exception('Authentication not initiated');
    }

    $authenticate->setDescription('Lorem Ipsum')
        ->setTransactionReference('TEST.1234')
        ->setCustomerReference('TEST.12345');

    $cse = [
        'identifier'    => $payload['identifier'],
        'data'          => $payload['data']
    ];
    $authenticate->setCse($cse);

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
