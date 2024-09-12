<?php

require_once('../vendor/autoload.php');
require_once('../config.php');

use Omnipay\Omnipay;
use Omnipay\Paynl\Message\Request\AuthorizeRequest;

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
    $authorize = $gateway->authorize($data);

    if (!$authorize instanceof AuthorizeRequest){
        throw new Exception('Authorization not initiated');
    }

    if (!isset($_POST['transaction_id']) || !isset($_POST['entrance_code'])) {
        throw new Exception('Authorization not successful');
    }

    $authorize->setOrderId($_POST['transaction_id'])
        ->setEntranceCode($_POST['entrance_code']);

    $cse = [
        'identifier'    => $_POST['identifier'],
        'data'          => $_POST['data']
    ];
    $authorize->setCse($cse);

    $authorize->setPayTdsAcquirerId($_POST['acquirer_id'])
        ->setPayTdsTransactionId($_POST['threeds_transaction_id']);

    $authorize->setTestMode(true);
    $result = $authorize->send();
    if (!$result->isSuccessful()) {
        throw new Exception($result->getMessage());
    }

    //Mimic the response of the demo, maybe this can be in paynl omnipay
    $response['result'] = "1";
    $response['orderId'] = $result->getTransactionOrderId();
    $response['entranceCode'] = $result->getTransactionEntranceCode();
    $response['nextAction'] = strtolower($result->getNextAction());

} catch (Exception $e) {
    $response = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($response);