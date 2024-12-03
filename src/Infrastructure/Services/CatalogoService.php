<?php

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;

class CatalogoService
{
    private $client;

    public function __construct(string $catalogoServiceUrl)
    {
        $this->client = new Client(['base_uri' => $catalogoServiceUrl]);
    }

    public function obterProduto(string $produtoId): ?array
    {
        try {
            $response = $this->client->get("/produtos/{$produtoId}");
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
