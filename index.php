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
    <form action="?submit" method="post" id="cseform">
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
        <?php
        //Not very clean, but acts as an example. Hidden input field can for example be replaced by httpd request in the js file
        $json = file_get_contents('https://payment.pay.nl/v1/Payment/getEncryptionKeys/json');
        $keys = json_decode($json);
        $key = json_encode([
            "identifier" => $keys->keys[0]['identifier'],
            "public_key" => $keys->keys[0]['public_key']
        ])
        ?>
        <input type="hidden" value="<?= $key; ?>" name="keys">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="cvv">CVV/CVC</label>
                <input class="form-control" id="cvv" type="text" name="cvv" value="123" placeholder="123">
            </div>
        </div>
    </div>
        <input class="btn btn-primary mt-3" type="submit">
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="main.js"></script>
</body>
</html>