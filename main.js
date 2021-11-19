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

    

    e.preventDefault();
});