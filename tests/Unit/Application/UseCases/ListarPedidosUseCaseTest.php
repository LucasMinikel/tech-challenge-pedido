<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\ListarPedidosUseCase;
use App\Domain\Entities\Pedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class ListarPedidosUseCaseTest extends TestCase
{
    private $pedidoRepository;
    private $listarPedidosUseCase;

    protected function setUp(): void
    {
        $this->pedidoRepository = $this->createMock(PedidoRepositoryInterface::class);
        $this->listarPedidosUseCase = new ListarPedidosUseCase($this->pedidoRepository);
    }

    public function testDeveListarPedidosComSucesso()
    {
        $pedidos = [
            new Pedido('PEDI123', '1', [], 100.00, 'CRIADO', new \DateTime()),
            new Pedido('PEDI456', '2', [], 150.00, 'EM_PREPARACAO', new \DateTime())
        ];

        $this->pedidoRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($pedidos);

        $resultado = $this->listarPedidosUseCase->execute();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertInstanceOf(PedidoDTO::class, $resultado[0]);
        $this->assertEquals('PEDI123', $resultado[0]->id);
        $this->assertEquals('PEDI456', $resultado[1]->id);
    }

    public function testDeveRetornarArrayVazioQuandoNaoHouverPedidos()
    {
        $this->pedidoRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $resultado = $this->listarPedidosUseCase->execute();

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }
}
