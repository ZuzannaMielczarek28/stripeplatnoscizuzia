<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Stripe – płatność testowa</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<h2>Płatność testowa (Stripe)</h2>

<label>Kwota (PLN):</label><br>
<input type="number" id="amount" step="0.01" value="49.99"><br><br>

<label>Opis:</label><br>
<input type="text" id="description" value="Testowa płatność"><br><br>

<button id="pay">Zapłać</button>

<hr>

<div id="payment-element"></div>
<div id="message"></div>

<script>
const stripe = Stripe("<?= getenv('STRIPE_PUBLISHABLE_KEY') ?>");

document.getElementById('pay').addEventListener('click', async () => {

    const amount = document.getElementById('amount').value;
    const description = document.getElementById('description').value;

    const response = await fetch('/create-intent.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount, description })
    });

    const data = await response.json();

    if (data.error) {
        document.getElementById('message').innerText = data.error;
        return;
    }

    const elements = stripe.elements({
        clientSecret: data.clientSecret
    });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: window.location.href
        }
    });

    if (error) {
        document.getElementById('message').innerText = error.message;
    }
});
</script>

</body>
</html>