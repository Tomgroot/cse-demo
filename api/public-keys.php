<?php
use Omnipay\Omnipay;

require_once('../vendor/autoload.php');
require_once('../config.php');

$gateway = Omnipay::create('Paynl');
$encryption = $gateway->fetchEncryptionKeys();
$result = $encryption->send();
$publicEncryptionKeys = $result->getKeys();

header('content-type: application/json');
echo json_encode($publicEncryptionKeys);

