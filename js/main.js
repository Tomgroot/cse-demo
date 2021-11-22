document.querySelector('form').addEventListener('submit', (e) => {
    const formData = new FormData(e.target);
    var data = {
        "cardholder": formData.get('name'),
        "cardnumber": formData.get('number'),
        "cardcvc": formData.get('cvv'),
        "valid_thru_month": formData.get("valid_thru_month"),
        "valid_thru_year": formData.get("valid_thru_year"),
    };

    // Because tested on local environment we use a hidden field, you can also use a http request to get the keys
    // const xhttp = new XMLHttpRequest();
    // xhttp.onload = function() {
    //     var keys = this.responseText;
    //     ...
    // }
    // xhttp.open("GET", "https://payment.pay.nl/v1/Payment/getEncryptionKeys/json");
    // xhttp.send();

    const keys = JSON.parse(formData.get('keys'));
    const public_key = b64_to_utf8(keys.public_key);

    var encrypt = new JSEncrypt();
    encrypt.setPublicKey(public_key);
    var cse = {
        "identifier": keys.identifier,
        "data": encrypt.encrypt(data)
    }

    //Submit only the encrypted card data
    var data = new FormData();
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        //Just for the example, we append the result in a div
        if (request.readyState == XMLHttpRequest.DONE) {
            $("#return").html(request.responseText);
        }
    }
    request.open("POST", "submit.php");
    data.append("cse", JSON.stringify(cse));
    request.send(data);

    e.preventDefault();
});

/**
 * Decode a base64 string into utf8
 * @param str
 * @returns {string}
 */
function b64_to_utf8( str ) {
    return decodeURIComponent(escape(window.atob( str )));
}