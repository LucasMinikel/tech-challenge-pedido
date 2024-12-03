<?php

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;

class ClienteService
{
    private $client;

    public function __construct(string $clienteServiceUrl)
    {
        $this->client = new Client(['base_uri' => $clienteServiceUrl]);
    }

    public function clienteExiste(string $clienteId): bool
    {
        try {
            $response = $this->client->get("/clientes/{$clienteId}");
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
