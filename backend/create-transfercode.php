<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

// POST validation
if (!isset($_POST['user'], $_POST['api_key'], $_POST['amount'])) {
    echo 'Invalid request';
    exit;
}

$user   = trim($_POST['user']);
$apiKey = trim($_POST['api_key']);
$amount = (float) $_POST['amount'];

if ($user === '' || $apiKey === '' || $amount <= 0) {
    echo 'All fields are required and amount must be positive';
    exit;
}

// Create HTTP client
$client = new Client([
    'base_uri' => 'https://yrgopelag.se/centralbank/',
    'timeout'  => 5.0,
]);

try {
    $response = $client->post('withdraw', [
        'json' => [
            'user'    => $user,
            'api_key' => $apiKey,
            'amount' => $amount,
        ],
    ]);

    $data = json_decode((string) $response->getBody(), true);

    if (isset($data['transferCode'])) {
        echo '<h2>Transfer code created</h2>';
        echo '<p><strong>Amount:</strong> $' . htmlspecialchars((string)$data['amount']) . '</p>';
        echo '<p><strong>Transfer code:</strong></p>';
        echo '<pre>' . htmlspecialchars($data['transferCode']) . '</pre>';
        echo '<p>Copy this code and use it when booking your room.</p>';
    } elseif (isset($data['error'])) {
        echo 'Error from central bank: ' . htmlspecialchars($data['error']);
    } else {
        echo 'Unexpected response from central bank';
    }
} catch (GuzzleException $e) {
    echo 'Could not contact central bank. Please try again later.';
}
