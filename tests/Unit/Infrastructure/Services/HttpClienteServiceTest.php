<?php

namespace Tests\Unit\Infrastructure\Services;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Services\HttpClienteService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class HttpClienteServiceTest extends TestCase
{
    public function testDeveRetornarTrueQuandoClienteExistir()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"id": "CLI123"}')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $clienteService = new HttpClienteService('http://api.teste.com');
        $reflection = new \ReflectionClass($clienteService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($clienteService, $client);

        $existe = $clienteService->clienteExiste('CLI123');

        $this->assertTrue($existe);
    }

    public function testDeveRetornarFalseQuandoClienteNaoExistir()
    {
        $mock = new MockHandler([
            new RequestException('Erro', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $clienteService = new HttpClienteService('http://api.teste.com');
        $reflection = new \ReflectionClass($clienteService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($clienteService, $client);

        $existe = $clienteService->clienteExiste('CLI999');

        $this->assertFalse($existe);
    }
}
