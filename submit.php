<?php
//This file handles submition and uses the omnipay paynl package to authenticate and authorize a CSE payment

# require autoloader
require_once('vendor/autoload.php');
require_once('config.php');

use Omnipay\Omnipay;

//If form is submitted we authorize the payment via CSE
if (!isset($_POST['cse']))
    exit;
//CSE only contains the encrypted CSE data, associative must be true as setCSE needs an array
$cse = json_decode($_POST['cse'], true);
if (!$cse)
    exit;

$gateway = Omnipay::create('Paynl');

$gateway->setApiToken(APITOKEN);
$gateway->setTokenCode(TOKENCODE);
$gateway->setServiceId(SERVICEID);

$data = ['amount' => 1, 'clientIp' => '84.247.45.3', 'returnUrl' => 'localhost'];


$authenticate = $gateway->authenticate($data);
$authenticate->setCse($cse);
$authenticate->setTestMode(true);
$authorizeResponse = $authenticate->send();

// 3D Secure
if ($authorizeResponse->getNextAction() == "tdsMethod") { ?>
<form method="POST" action="<?= $authorizeResponse->getThreeDSMethodUrl() ?>">
    <input name="threeDSMethodData" value="<?= $authorizeResponse->getThreeDsMethodData() ?>" />
    <script type="text/javascript">(function(){ document.querySelector("form").submit() })();</script>
</form>
<?php
} else {
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
        <pre>
            <?php echo json_encode($authorizeResponse->getData(), JSON_PRETTY_PRINT); ?>
            <?php var_dump($cse); ?>
        </pre>
    </div>
<?php } ?>