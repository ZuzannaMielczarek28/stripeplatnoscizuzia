<?php
require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['amount'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Brak kwoty']);
    exit;
}

$amount = round(floatval($data['amount']) * 100); // PLN → grosze
$description = $data['description'] ?? 'Płatność testowa';

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'pln',
        'description' => $description,
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
        'paymentIntentId' => $paymentIntent->id,
        'status' => $paymentIntent->status,
        'amount' => $amount,
        'currency' => 'pln'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Błąd Stripe',
        'details' => $e->getMessage()
    ]);
}