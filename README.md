# CSE fields
This repository is a front-end implementation of credit card input encryption using JSEncrypt based on Pay.nl's CSE demo. It detects which card type is filled in. Using this information it decides when to `postMessage` the encrypted result such that it can be used inside an `iframe` where the parent is listening to messages comming from this instance. 

## Installation
1. `yarn install`
2. Replace `http://localhost:9999` in the index.html file with your address.


![image](https://github.com/user-attachments/assets/af4813c1-41a2-4cac-abfd-feeae717fe1c)
