<?php

namespace Tests\Unit\Infrastructure\API\Controllers;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\API\Controllers\PedidoController;
use App\Application\UseCases\CriarPedidoUseCase;
use App\Application\UseCases\AtualizarStatusPedidoUseCase;
use App\Application\UseCases\ListarPedidosUseCase;
use App\Application\UseCases\ObterPedidoUseCase;
use App\Application\DTOs\PedidoDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class PedidoControllerTest extends TestCase
{
    private $criarPedidoUseCase;
    private $atualizarStatusPedidoUseCase;
    private $listarPedidosUseCase;
    private $obterPedidoUseCase;
    private $controller;
    private $request;
    private $response;
    private $stream;

    protected function setUp(): void
    {
        $this->criarPedidoUseCase = $this->createMock(CriarPedidoUseCase::class);
        $this->atualizarStatusPedidoUseCase = $this->createMock(AtualizarStatusPedidoUseCase::class);
        $this->listarPedidosUseCase = $this->createMock(ListarPedidosUseCase::class);
        $this->obterPedidoUseCase = $this->createMock(ObterPedidoUseCase::class);

        $this->controller = new PedidoController(
            $this->criarPedidoUseCase,
            $this->atualizarStatusPedidoUseCase,
            $this->listarPedidosUseCase,
            $this->obterPedidoUseCase
        );

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->stream = $this->createMock(StreamInterface::class);
    }

    public function testCriarPedidoComSucesso()
    {
        $pedidoData = [
            'clienteId' => '123',
            'itens' => [
                ['produtoId' => '1', 'quantidade' => 2, 'precoUnitario' => 10.00]
            ]
        ];

        $this->stream->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode($pedidoData));

        $this->request->expects($this->once())
            ->method('getBody')
            ->willReturn($this->stream);

        $pedidoDTO = $this->createMock(PedidoDTO::class);
        $pedidoDTO->expects($this->once())
            ->method('toArray')
            ->willReturn($pedidoData);

        $this->criarPedidoUseCase->expects($this->once())
            ->method('execute')
            ->willReturn($pedidoDTO);

        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $this->response->expects($this->once())
            ->method('withStatus')
            ->with(201)
            ->willReturnSelf();

        $result = $this->controller->criar($this->request, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testListarPedidos()
    {
        $pedidos = [
            $this->createMock(PedidoDTO::class),
            $this->createMock(PedidoDTO::class)
        ];

        $this->listarPedidosUseCase->expects($this->once())
            ->method('execute')
            ->willReturn($pedidos);

        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $result = $this->controller->listar($this->request, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testObterPedido()
    {
        $pedidoId = '123';
        $pedidoDTO = $this->createMock(PedidoDTO::class);

        $pedidoDTO->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'id' => $pedidoId,
                'clienteId' => '456',
                'itens' => [],
                'valorTotal' => 100.00,
                'status' => 'CRIADO'
            ]);

        $this->request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($pedidoId);

        $this->obterPedidoUseCase->expects($this->once())
            ->method('execute')
            ->with($pedidoId)
            ->willReturn($pedidoDTO);

        $this->response->expects($this->once())
            ->method('getBody')
            ->willReturn($this->stream);

        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $result = $this->controller->obter($this->request, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
