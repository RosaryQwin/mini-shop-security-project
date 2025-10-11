// googlepay.js
// Integration of Google Pay as one of the payment methods

// Step 1: Define base request version for Google Pay API
const baseRequest = { apiVersion: 2, apiVersionMinor: 0 };

// Step 2: Specify allowed card networks and authentication methods
const allowedCardNetworks = ["VISA", "MASTERCARD", "AMEX"];
const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

// Step 3: Set tokenization specification (in real project this is linked to payment gateway)
const tokenizationSpecification = {
  type: 'PAYMENT_GATEWAY',
  parameters: { 
    'gateway': 'example', 
    'gatewayMerchantId': 'exampleGatewayMerchantId' 
  }
};

// Step 4: Define card payment method
const baseCardPaymentMethod = {
  type: 'CARD',
  parameters: { 
    allowedAuthMethods: allowedCardAuthMethods, 
    allowedCardNetworks: allowedCardNetworks 
  }
};
const cardPaymentMethod = Object.assign({ tokenizationSpecification }, baseCardPaymentMethod);

let paymentsClient = null;

// Step 5: Initialize Google Pay client (using Sandbox "TEST" mode)
function getGooglePaymentsClient() {
  if (!paymentsClient) {
    paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
  }
  return paymentsClient;
}

// Step 6: Create a request to check if Google Pay is available on the device
function getGoogleIsReadyToPayRequest() {
  return Object.assign({}, baseRequest, { allowedPaymentMethods: [baseCardPaymentMethod] });
}

// Step 7: Load Google Pay when the script is ready and show the button if available
function onGooglePayLoaded() {
  const client = getGooglePaymentsClient();
  client.isReadyToPay(getGoogleIsReadyToPayRequest())
    .then(function(response) {
      if (response.result) addGooglePayButton(); // add button if supported
    })
    .catch(console.error);
}

// Step 8: Dynamically create the Google Pay button and attach it to checkout page
function addGooglePayButton() {
  const client = getGooglePaymentsClient();
  const button = client.createButton({ onClick: onGooglePaymentButtonClicked });
  document.getElementById('gpay-button').appendChild(button);
}

// Step 9: Build payment request with order total, currency, and merchant information
function getPaymentDataRequest(totalPrice) {
  return Object.assign({}, baseRequest, {
    allowedPaymentMethods: [cardPaymentMethod],
    transactionInfo: {
      totalPriceStatus: "FINAL",
      totalPrice: totalPrice.toString(),
      currencyCode: "AUD",
      countryCode: "AU"
    },
    merchantInfo: { merchantName: "s3927657 Mini Shop" }
  });
}

// Step 10: Handle click event of Google Pay button and open payment popup
function onGooglePaymentButtonClicked() {
  const total = document.getElementById("cart-total").value;
  const request = getPaymentDataRequest(total);
  const client = getGooglePaymentsClient();

  client.loadPaymentData(request)
    .then(function(paymentData) {
      processPayment(paymentData, total); // send result for processing
    })
    .catch(console.error);
}

// Step 11: Process Google Pay payment result
function processPayment(paymentData, total) {
  const token = paymentData.paymentMethodData.tokenizationData.token;
  // In real production, token is sent to backend server to complete payment

  // Step 12: Collect address from checkout form
  const addressData = {
    address_name: document.getElementById("fullname").value,
    address_street: document.getElementById("street").value,
    address_city: document.getElementById("city").value,
    address_state: document.getElementById("state").value,
    address_zip: document.getElementById("zip").value,
    address_country: document.getElementById("country").value
  };

  // Step 13: Save address into session using AJAX call
  fetch("Payments/save_address.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(addressData)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Step 14: Redirect user to success page with payment details
      window.location.href =
        "Payments/success.php?method=gpay&st=Completed&amt=" +
        total +
        "&cc=AUD&tx=GPAY-" +
        Date.now();
    } else {
      alert("❌ Failed to save address");
    }
  })
  .catch(console.error);
}
