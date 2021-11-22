<?php
# require autoloader
require_once('vendor/autoload.php');
require_once('config.php');

use Omnipay\Omnipay;

//If form is submitted we authorize the payment via CSE
if (!isset($_POST['cse']))
    exit;
//CSE only contains the encrypted CSE data
$cse = json_decode($_POST['cse']);
if (!$cse)
    exit;

$gateway = Omnipay::create('Paynl');

$gateway->setApiToken(APITOKEN);
$gateway->setTokenCode(TOKENCODE);
$gateway->setServiceId(SERVICEID);

$data = ['amount' => 1, 'clientIp' => '84.247.45.3', 'returnUrl' => 'localhost'];
$authorize = $gateway->authorize($data);
$authorize->setCse($cse);
$authorize->setTestMode(true);
$authorizeResponse = $authorize->send();
?>
    <div class="mt-3 alert alert-<?= $authorizeResponse->isSuccessful() ? 'success' : 'danger'; ?>" role="alert">
        <?= $authorizeResponse->getMessage(); ?>
    </div>
    <div class="mt-3">
        <label class="form-label">Response:</label>
        <textarea class="form-control" style="width: 600px; height: 300px;">
            <?php var_dump($authorizeResponse->getData()); ?>
        </textarea>
    </div>