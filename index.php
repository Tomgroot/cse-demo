<?php
use Omnipay\Omnipay;

require_once('vendor/autoload.php');
require_once('config.php');

try {
    $gateway = Omnipay::create('Paynl');
    $encryption = $gateway->fetchEncryptionKeys();
    $result = $encryption->send();
    $publicEncryptionKeys = $result->getKeys();
} catch (Exception $exception) {
    die ('<h1>Unexpected error</h1><p>' . $exception->getMessage() . '</p>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSE Demo</title>
    <link rel="stylesheet" type="text/css" href="css/cryptography-demo.css">
    <script>
        var keyUrl = 'public-keys.php';
        var keyPairs = '<?php echo json_encode($publicEncryptionKeys); ?>';
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
<div class="container pt-5 rounded-box">

    <h1>CSE demo</h1>
    <form action="api/process.php" method="post" data-pay-encrypt-form>
        <fieldset>
            <label for="card-holder">Kaarthouder</label>
            <input id="card-holder" type="text" class="form-control" placeholder="Naam van de kaarthouder" name="cardholder" value="" required data-pay-encrypt-field />
        </fieldset>

        <fieldset class="mt-3">
            <div class="row-pan-cvc-group">
                <div class="column-pan col-8">
                    <div class="form-element-container has-icon-container">
                        <label for="cardnumber">Kaartnummer</label>
                        <div class="has-icon-wrap form-group">
                            <span class="input-container">
                                <input id="cardnumber" class="form-control" type="text" name="cardnumber" placeholder="Het nummer van uw credit- of debitkaart" value="" required data-pay-encrypt-field />
                            </span>

                            <div class="icon-container">
                                <img src="img/creditcard/cc-front.svg" data-credit-card-type alt="Card type" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column-cvc col-4 ps-3">
                    <div class="form-element-container form-group">
                        <label for="cvc" data-cvc-label>CVC</label>
                        <span class="input-container">
                            <input id="cvc" type="text" name="cardcvc" class="form-control" placeholder="123" required value=""  data-pay-encrypt-field />
                        </span>
                    </div>
                </div>
            </div>

            <div class="row-expiry-group">
                <div class="column-month col-6 pe-2">
                    <div class="form-element-container form-group">
                        <label for="month">Geldig tot maand</label>
                        <select name="valid_thru_month" class="form-control" id="month" data-pay-encrypt-field>
                            <option value="" disabled selected>Kies</option>
                            <option value="01">01 - Januari</option>
                            <option value="02">02 - Februari</option>
                            <option value="03">03 - Maart</option>
                            <option value="04">04 - April</option>
                            <option value="05">05 - Mei</option>
                            <option value="06">06 - Juni</option>
                            <option value="07">07 - Juli</option>
                            <option value="08">08 - Augustus</option>
                            <option value="09">09 - September</option>
                            <option value="10">10 - Oktober</option>
                            <option value="11">11 - November</option>
                            <option value="12">12 - December</option>
                        </select>
                    </div>
                </div>
                <div class="column-year col-6 ps-2">
                    <div class="form-element-container form-group">
                        <label for="year">Jaar</label>
                        <select name="valid_thru_year" id="year" class="form-control" data-pay-encrypt-field>
                            <option value="" disabled selected>Kies</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-element-container">
                <button type="submit" class="btn btn-primary" data-loading-state="0" disabled="disabled">
                    <span class="state-not-loading">Doorgaan</span>
                    <span class="state-loading">
                        <img src="img/spinner.gif" width="30" height="30" alt="Loading" />
                    </span>
                </button>
            </div>
        </fieldset>
    </form>
</div>

<script type="module">
    import MyForm from './js/merchant.js';

    window.addEventListener("DOMContentLoaded", () => {
        let form = new MyForm;
        form.init();
    });
</script>
<div id="payment-modal" class="modal micromodal-slide" aria-hidden="true"></div>
</body>
</html>
