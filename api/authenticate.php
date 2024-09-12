<?php

require_once('../vendor/autoload.php');
require_once('../config.php');

use Omnipay\Omnipay;
use Omnipay\Paynl\Message\Request\AuthenticateRequest;

try {
    if (!isset($_POST['identifier']) || !isset($_POST['data'])) {
        throw new Exception('Missing payload');
    }

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid json');
    }

    $gateway = Omnipay::create('Paynl');
    $gateway->setApiToken(APITOKEN);
    $gateway->setTokenCode(TOKENCODE);
    $gateway->setServiceId(SERVICEID);

    $data = ['amount' => AMOUNT, 'clientIp' => $_SERVER['REMOTE_ADDR'], 'returnUrl' => RETURN_URL];
    $authenticate = $gateway->authenticate($data);

    if (!$authenticate instanceof AuthenticateRequest){
        throw new Exception('Authentication not initiated');
    }

    $authenticate->setDescription('Lorem Ipsum')
        ->setTransactionReference('TEST.1234')
        ->setCustomerReference('TEST.12345');

    $cse = [
        'identifier'    => $_POST['identifier'],
        'data'          => $_POST['data']
    ];
    $authenticate->setCse($cse);
    $authenticate->setTestMode(true);

    $authenticate
        ->setJavaEnabled('false')
        ->setJavascriptEnabled('false')
        ->setLanguage('nl-NL')
        ->setColorDepth('24')
        ->setScreenWidth('1920')
        ->setScreenHeight('1080')
        ->setTz('-120');

    $result = $authenticate->send();

    if (!$result->isSuccessful()) {
        throw new Exception($result->getMessage());
    }

    //Mimic the response of the demo
    $response = $result->getThreeDS();
    $response['result'] = $result->isSuccessful() ? "1" : "0";
    $response['entranceCode'] = $result->getTransactionEntranceCode();
    $response['orderId'] = $result->getTransactionOrderId();
    $response['transaction'] = [
        'entranceCode'  => $result->getTransactionEntranceCode(),
        'transactionId' => $result->getTransactionOrderId()
    ];

} catch (Exception $e) {
    $response = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($response);
