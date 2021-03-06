<?php

use Omnipay\Omnipay;

require_once('../vendor/autoload.php');
require_once('../config.php');

try {
    $gateway = Omnipay::create('Paynl');
    $gateway->setApiToken(APITOKEN);
    $gateway->setTokenCode(TOKENCODE);
    $gateway->setServiceId(SERVICEID);
    $fetch_transaction = $gateway->fetchTransaction();
    $fetch_transaction->setTransactionReference(filter_var($_GET['orderId'], FILTER_SANITIZE_STRING));
    $result = $fetch_transaction->send();
    if (!$result->isSuccessful()) {
        throw new Exception($result->getMessage());
    }
    $response = $result->getData();
} catch (\Exception $e) {
    $response = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

?>
<html>
<body>
    <pre><?php echo print_r($response);?></pre>
</body>
</html>
