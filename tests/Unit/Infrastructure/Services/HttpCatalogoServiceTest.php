<?php

namespace Tests\Unit\Infrastructure\Services;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Services\HttpCatalogoService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class HttpCatalogoServiceTest extends TestCase
{
    public function testDeveRetornarProdutoQuandoExistir()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['id' => 'PROD123', 'preco' => 100.00]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $catalogoService = new HttpCatalogoService('http://api.teste.com');
        $reflection = new \ReflectionClass($catalogoService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($catalogoService, $client);

        $produto = $catalogoService->obterProduto('PROD123');

        $this->assertNotNull($produto);
        $this->assertEquals('PROD123', $produto['id']);
        $this->assertEquals(100.00, $produto['preco']);
    }

    public function testDeveRetornarNullQuandoProdutoNaoExistir()
    {
        $mock = new MockHandler([
            new RequestException('Erro', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $catalogoService = new HttpCatalogoService('http://api.teste.com');
        $reflection = new \ReflectionClass($catalogoService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($catalogoService, $client);

        $produto = $catalogoService->obterProduto('PROD999');

        $this->assertNull($produto);
    }
}
