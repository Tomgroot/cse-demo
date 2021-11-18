<?php

# require autoloader
require_once('vendor/autoload.php');
require_once('config.php');

use Omnipay\Omnipay;

//If form is submitted we authorize the payment via CSE
if (isset($_GET['submit']) && isset($_POST['name'])) {

    $gateway = Omnipay::create('Paynl');

    $gateway->setApiToken(APITOKEN);
    $gateway->setTokenCode(TOKENCODE);
    $gateway->setServiceId(SERVICEID);

    $data = ['amount' => 1, 'clientIp' => '84.247.45.3', 'returnUrl' => 'localhost'];

    $authorize = $gateway->authorize($data);
    $card = new \Omnipay\Common\CreditCard([
        'name' => $_POST['name'],
        'cvv' => $_POST['cvv'],
        'expiryYear' => $_POST['expiryYear'],
        'expiryMonth' => $_POST['expiryMonth'],
        'number' => $_POST['number']
    ]);

    $authorize->setCse($card);
    $authorize->setTestMode(true);
    $authorizeResponse = $authorize->send();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>CSE demo</title>
</head>
<body>
<div class="container pt-5">

    <h1>CSE Demo</h1>
    <form action="?submit" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" id="name" type="text" name="name" value="T.W. Groot" placeholder="Enter your name">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="ccnumber">Credit Card Number</label>
                <div class="input-group">
                    <input class="form-control" type="text" name="number" value="4111 1111 1111 1111" placeholder="0000 0000 0000 0000" autocomplete="email">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="ccmonth">Expiry month</label>
            <select class="form-control" id="ccmonth" name="expiryMonth">
                <option value="01">1</option>
                <option value="02">2</option>
                <option selected value="03">3</option>
                <option value="04">4</option>
                <option value="05">5</option>
                <option value="06">6</option>
                <option value="07">7</option>
                <option value="08">8</option>
                <option value="09">9</option>
                <option>10</option>
                <option>11</option>
                <option>12</option>
            </select>
        </div>
        <div class="form-group col-sm-4">
            <label for="ccyear">Expiry year</label>
            <select class="form-control" id="ccyear" name="expiryYear">
                <option>2014</option>
                <option>2015</option>
                <option>2016</option>
                <option>2017</option>
                <option>2018</option>
                <option>2019</option>
                <option>2020</option>
                <option>2021</option>
                <option>2022</option>
                <option selected>2023</option>
                <option>2024</option>
                <option>2025</option>
            </select>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="cvv">CVV/CVC</label>
                <input class="form-control" id="cvv" type="text" name="cvv" value="123" placeholder="123">
            </div>
        </div>
    </div>
        <input class="btn btn-primary mt-3" type="submit">
    </form>

    <?php if (isset($_GET['submit']) && isset($_POST['name'])) { ?>
        <div class="mt-3 alert alert-<?= $authorizeResponse->isSuccessful() ? 'success' : 'danger'; ?>" role="alert">
            <?= $authorizeResponse->getMessage(); ?>
        </div>
        <div class="mt-3">
            <label class="form-label">Response:</label>
            <textarea class="form-control" style="width: 600px; height: 300px;">
            <?php var_dump($authorizeResponse->getData()); ?>
            </textarea>
        </div>
    <?php } ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>