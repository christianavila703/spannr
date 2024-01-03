let csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
let stripeCheckoutData = document.getElementById('stripe-checkout-data');
let stripeKey = stripeCheckoutData.getAttribute('data-stripe-key');
const stripe = Stripe(stripeKey);
let setupIntentUrl = stripeCheckoutData.getAttribute('data-checkout-url');
let returnUrl = stripeCheckoutData.getAttribute('data-return-url');

let loadingMessageElement = document.getElementById('loading-message');

let elements;

let appearance = {
    theme: 'flat',
    variables: {
        colorPrimary: '#0ac7fe',
        colorBackground: '#efefef',
        colorText: '#212529',
        colorTextPlaceholder: '#6c757d',
        colorDanger: '#ed4b00',
        iconColor: '#ed008c',
        fontFamily: '"Helvetica Neue", Avenir, "Helvetica", Arial, sans-serif',
        spacingUnit: '2.5px',
        borderRadius: '0',
        fontSizeBase: '18px'
    }
};

document.querySelector("#payment-form").addEventListener("submit", handleSubmit);

async function initialize() {
    const {clientSecret} = await fetch(setupIntentUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-Token": csrfToken
        },
        credentials: "same-origin",
    }).then((r) => r.json());

    elements = stripe.elements({clientSecret, appearance});

    const paymentElementOptions = {
        layout: "tabs",
    };

    const paymentElement = elements.create("payment", paymentElementOptions);
    paymentElement.mount("#payment-element");
    loadingMessageElement.classList.add('visually-hidden');
    document.querySelector("#submit").classList.remove('visually-hidden');
}

async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const {error} = await stripe.confirmSetup({
        elements,
        confirmParams: {
            return_url: returnUrl
        },
    });

    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        showMessage("An unexpected error occurred.");
    }

    setLoading(false);
}

async function checkStatus() {
  const clientSecret = new URLSearchParams(window.location.search).get(
    "setup_intent_client_secret"
  );

  if (!clientSecret) {
    return;
  }

  const { setupIntent } = await stripe.retrievePaymentIntent(clientSecret);

  switch (setupIntent.status) {
    case "succeeded":
      showMessage("Payment succeeded!");
      break;
    case "processing":
      showMessage("Your payment is processing.");
      break;
    case "requires_payment_method":
      showMessage("Your payment was not successful, please try again.");
      break;
    default:
      showMessage("Something went wrong.");
      break;
  }
}

function showMessage(messageText) {
  const messageContainer = document.querySelector("#payment-message");

  messageContainer.classList.remove("visually-hidden");
  messageContainer.textContent = messageText;

  setTimeout(function () {
    messageContainer.classList.add("visually-hidden");
    messageContainer.textContent = "";
  }, 7500);
}

function setLoading(isLoading) {
  if (isLoading) {
    // Disable the button and show a spinner
    document.querySelector("#submit").disabled = true;
    document.querySelector("#spinner").classList.remove("visually-hidden");
    document.querySelector("#button-text").classList.add("visually-hidden");
  } else {
    document.querySelector("#submit").disabled = false;
    document.querySelector("#spinner").classList.add("visually-hidden");
    document.querySelector("#button-text").classList.remove("visually-hidden");
  }
}

let loadFormManuallyButton = document.getElementById('load-job-subscription-update-card-form');
loadFormManuallyButton.addEventListener('click', function(event) {
    initialize();
    checkStatus();
});

window.addEventListener("load", (event) => {
    initialize();
    checkStatus();
});
