<?php

require_once('../vendor/autoload.php');
require_once('../config.php');

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

    if (isset($_POST['transaction_id'])) {
        $authenticate->setOrderId($_POST['transaction_id'])
            ->setEntranceCode($_POST['entrance_code']);
    } else {
        $authenticate->setDescription('Lorem Ipsum')
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

    $authenticate
        ->setJavaEnabled('false')
        ->setJavascriptEnabled('false')
        ->setLanguage('nl-NL')
        ->setColorDepth('24')
        ->setScreenWidth('1920')
        ->setScreenHeight('1080')
        ->setTz('-120');

    $authenticate->setTestMode(true);
    $result = $authenticate->send();

    if (!$result->isSuccessful()) {
        throw new Exception($result->getMessage());
    }

    //Mimic the response of the demo, maybe this can be in paynl omnipay
    $response = $result->getThreeDS();
    $response['result'] = "1";
    $transaction = $result->getTransaction();
    $response['entranceCode'] = $transaction['entranceCode'] ?? "";
    $response['orderId'] = $transaction['orderId'] ?? "";
    $response['transaction'] = [
        'entranceCode'  => $response['entranceCode'],
        'transactionId' => $response['orderId']
    ];

} catch (Exception $e) {
    $response = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($response);
