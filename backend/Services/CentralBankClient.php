<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CentralBankClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://yrgopelag.se/centralbank/',
            'timeout'  => 5.0,
        ]);
    }

    public function validateTransferCode(string $transferCode, int $totalCost): bool
    {
        try {
            $response = $this->client->post('transferCode', [
                'json' => [
                    'transferCode' => $transferCode,
                    'totalCost'    => $totalCost,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return isset($data['status']) && $data['status'] === 'success';
        } catch (GuzzleException $e) {
            return false;
        }
    }

    public function deposit(string $user, string $transferCode): bool
    {
        try {
            $response = $this->client->post('deposit', [
                'json' => [
                    'user'         => $user,
                    'transferCode' => $transferCode,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return isset($data['status']) && $data['status'] === 'success';
        } catch (GuzzleException $e) {
            error_log('CentralBankClient error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReceipt(
        string $hotelOwner,
        string $guestName,
        string $arrival,
        string $departure,
        array $featuresUsed,
        int $starRating
    ): bool {
        try {
            $response = $this->client->post('receipt', [
                'json' => [
                    'user'           => $hotelOwner,
                    'api_key'        => $_ENV['API_KEY'],
                    'guest_name'     => $guestName,
                    'arrival_date'   => $arrival,
                    'departure_date' => $departure,
                    'features_used'  => $featuresUsed,
                    'star_rating'    => $starRating,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return isset($data['status']) && $data['status'] === 'success';
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
