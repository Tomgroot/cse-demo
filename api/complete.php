<?php

require_once('vendor/autoload.php');
require_once('config.php');

try {
    $gateway = Omnipay::create('Paynl');
    $fetch_transaction = $gateway->fetchTransaction();
    $fetch_transaction->setTransactionId(filter_var($_GET['orderId'], FILTER_SANITIZE_STRING));
    $result = $fetch_transaction->send();
} catch (\Exception $e) {
    $result = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

?>
<html>
<body>
    <pre><?php echo print_r($result->getData());?></pre>
</body>
</html>
