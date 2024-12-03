<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\ObterPedidoUseCase;
use App\Domain\Entities\Pedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class ObterPedidoUseCaseTest extends TestCase
{
    private $pedidoRepository;
    private $obterPedidoUseCase;

    protected function setUp(): void
    {
        $this->pedidoRepository = $this->createMock(PedidoRepositoryInterface::class);
        $this->obterPedidoUseCase = new ObterPedidoUseCase($this->pedidoRepository);
    }

    public function testDeveObterPedidoComSucesso()
    {
        $pedido = new Pedido(
            'PEDI123',
            '1',
            [],
            100.00,
            'CRIADO',
            new \DateTime()
        );

        $this->pedidoRepository
            ->expects($this->once())
            ->method('findById')
            ->with('PEDI123')
            ->willReturn($pedido);

        $resultado = $this->obterPedidoUseCase->execute('PEDI123');

        $this->assertInstanceOf(PedidoDTO::class, $resultado);
        $this->assertEquals('PEDI123', $resultado->id);
        $this->assertEquals('1', $resultado->clienteId);
        $this->assertEquals(100.00, $resultado->valorTotal);
        $this->assertEquals('CRIADO', $resultado->status);
    }

    public function testDeveLancarExcecaoQuandoPedidoNaoExiste()
    {
        $this->pedidoRepository
            ->expects($this->once())
            ->method('findById')
            ->with('PEDI999')
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Pedido não encontrado');

        $this->obterPedidoUseCase->execute('PEDI999');
    }
}
