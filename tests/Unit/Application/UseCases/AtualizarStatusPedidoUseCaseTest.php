<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\AtualizarStatusPedidoUseCase;
use App\Domain\Entities\Pedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class AtualizarStatusPedidoUseCaseTest extends TestCase
{
    private $pedidoRepository;
    private $atualizarStatusPedidoUseCase;

    protected function setUp(): void
    {
        $this->pedidoRepository = $this->createMock(PedidoRepositoryInterface::class);
        $this->atualizarStatusPedidoUseCase = new AtualizarStatusPedidoUseCase($this->pedidoRepository);
    }

    public function testDeveAtualizarStatusDoPedidoComSucesso()
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

        $this->pedidoRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function ($pedidoAtualizado) {
                return $pedidoAtualizado->getStatus() === 'EM_PREPARACAO';
            }))
            ->willReturn(true);

        $resultado = $this->atualizarStatusPedidoUseCase->execute('PEDI123', 'EM_PREPARACAO');

        $this->assertInstanceOf(PedidoDTO::class, $resultado);
        $this->assertEquals('EM_PREPARACAO', $resultado->status);
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

        $this->atualizarStatusPedidoUseCase->execute('PEDI999', 'EM_PREPARACAO');
    }
}
