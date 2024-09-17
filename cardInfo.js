export const useCardInfo = () => {
    const jsEncrypt = new JSEncrypt();
    let currentCardType = 'general';
    const scrollHeight = document.documentElement.scrollHeight;
    window.parent.postMessage({scrollHeight}, '*');

    const cardTypes = {
        uatp: {
            blocks: [4, 5, 6],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-front.svg',
            re: /^(?!1800)1\d{0,14}/,
        },
        amex: {
            blocks: [4, 6, 5],
            code: { name: "CID", size: 4 },
            image: 'img/creditcard/cc-amex.svg',
            re: /^3[47]\d{0,13}/,
        },
        diners: {
            blocks: [4, 6, 4],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-diners-club.svg',
            re: /^3(?:0([0-5]|9)|[689]\d?)\d{0,11}/,
        },
        discover: {
            blocks: [4, 4, 4, 4],
            code: { name: "CID", size: 3 },
            image: 'img/creditcard/cc-discover.svg',
            re: /^(?:6011|65\d{0,2}|64[4-9]\d?)\d{0,12}/,
        },
        mastercard: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVC", size: 3 },
            image: 'img/creditcard/cc-mastercard.svg',
            re: /^(5[1-5]\d{0,2}|22[2-9]\d{0,1}|2[3-7]\d{0,2})\d{0,12}/,
        },
        dankort: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVV", size: 3 },
            re: /^(5019|4175|4571)\d{0,12}/,
        },
        instapayment: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVV", size: 3 },
            re: /^63[7-9]\d{0,13}/,
        },
        jcb15: {
            blocks: [4, 6, 5],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-jcb.svg',
            re: /^(?:2131|1800)\d{0,11}/,
        },
        jcb: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-jcb.svg',
            re: /^(?:35\d{0,2})\d{0,12}/,
        },
        maestro: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVC", size: 3 },
            image: 'img/creditcard/cc-maestro.svg',
            re: /^(?:5[0678]\d{0,2}|6304|67\d{0,2})\d{0,12}/,
        },
        visa: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-visa.svg',
            re: /^4\d{0,15}/,
        },
        mir: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVP2", size: 3 },
            image: 'img/creditcard/cc-mir.svg',
            re: /^220[0-4]\d{0,12}/,
        },
        unionPay: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVN", size: 3 },
            image: 'img/creditcard/cc-unionpay.svg',
            re: /^(62|81)\d{0,14}/
        },
        general: {
            blocks: [4, 4, 4, 4],
            code: { name: "CVV", size: 3 },
            image: 'img/creditcard/cc-front.svg',
        }
    }

    function setPublicKey(publicKey) {
        jsEncrypt.setPublicKey(atob(publicKey));
    }

    function getStrictBlocks(block) {
        const total = block.reduce(function (prev, current) {
            return prev + current;
        }, 0);

        return block.concat(19 - total);
    }

    function getCurrentCardType() {
        return {
            ...cardTypes[currentCardType],
            cardNumberLength: cardTypes[currentCardType].blocks.reduce((a, b) => a + b, 0)
        };
    }

    function setCurrentCardTypeBasedOnCardNumber(cardNumber) {
        const cardNumbersOnly = cardNumber.replace(/\D/g, '');
        for (const [key, card] of Object.entries(cardTypes)) {
            if (!card.re || !card.blocks) {
                continue;
            }

            if (card.re.test(cardNumbersOnly)) {
                currentCardType = key;
                return;
            }
        }

        currentCardType = 'general';
    }

    function parseExpirationInputString(value) {
        const numbers = value.replace(/\D/g, '');

        if (numbers.length === 1 && value.includes('/')) {
            return '0' + numbers + ' / ';
        }

        if (value.length === 4) {
            return numbers;
        }

        if (numbers.length === 2) {
            return numbers + ' / ';
        }

        if (numbers.length > 2) {
            return numbers.substring(0, 2) + ' / ' + numbers.substring(2);
        }

        return numbers;
    }

    function parseCardNumberInputString(cardNumber) {
        const cardNumbersOnly = cardNumber.replace(/\D/g, '');
        let whitespacedNumber = '';
        let blockStart = 0;
        const cardInfo = getCurrentCardType();
        for (const blockLength of cardInfo.blocks) {
            whitespacedNumber += cardNumbersOnly.substring(blockStart, blockStart + blockLength) + ' ';
            blockStart += blockLength;
        }

        return whitespacedNumber.trim();
    }

    function parseExpiryYear(expiryYear) {
        const expiryYearTwoDigits = parseInt(expiryYear);
        const currentYear = new Date().getFullYear();
        const currentCentury = Math.floor(currentYear / 100);
        const currentYearTwoDigits = currentYear % 100;

        return String((expiryYearTwoDigits <= currentYearTwoDigits)
            ? ((currentCentury + 1) * 100 + expiryYearTwoDigits)
            : (currentCentury * 100 + expiryYearTwoDigits));
    }

    function encryptAndSubmit(cardHolder, cardNumber, cardCvc, validThruMonth, validThruYear) {
        const dataToEncrypt = {
            browserJavaEnabled: navigator.javaEnabled(),
            browserJavascriptEnabled: true,
            browserLanguage: navigator.language,
            browserColorDepth: screen.colorDepth,
            browserScreenWidth: screen.width,
            browserScreenHeight: screen.height,
            browserTZ: new Date().getTimezoneOffset(),
            cardholder: cardHolder,
            cardnumber: cardNumber,
            cardcvc: cardCvc,
            valid_thru_month: validThruMonth,
            valid_thru_year: validThruYear,
        };

        const encrypted = jsEncrypt.encrypt(JSON.stringify(dataToEncrypt));

        window.parent.postMessage({encrypted}, '*');
    }

    function submitWhenCardDetailsAreFilledIn(cardHolder, cardNumber, validThru, cardCvc) {
        const cardHolderParsed = cardHolder.replace(/[^a-zA-Z\s]/g, '').replace(/\s+/g, ' ').trim();
        const cardNumberParsed = cardNumber.replace(/\D/g, '');
        const cardCvcParsed = cardCvc.replace(/\D/g, '');
        const validThruParsed = validThru.split(' / ').map(value => value.replace(/\D/g, ''));
        const cardInfo = getCurrentCardType();

        if (cardHolderParsed.length > 0 && cardNumberParsed.length >= cardInfo.cardNumberLength
            && cardCvcParsed.length === cardInfo.code.size && validThruParsed.length === 2
            && validThruParsed[0].length === 2 && validThruParsed[1].length === 2) {
            encryptAndSubmit(
                cardHolderParsed,
                cardNumberParsed,
                cardCvcParsed,
                validThruParsed[0],
                parseExpiryYear(validThruParsed[1])
            );
        }
    }

    return {
        cardTypes,
        parseExpirationInputString,
        parseCardNumberInputString,
        submitWhenCardDetailsAreFilledIn,
        setCurrentCardTypeBasedOnCardNumber,
        setPublicKey,
        getCurrentCardType
    };
};
