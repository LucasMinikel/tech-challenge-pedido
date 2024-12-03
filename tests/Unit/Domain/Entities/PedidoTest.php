<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use App\Domain\Entities\Pedido;
use App\Domain\Entities\ItemPedido;

class PedidoTest extends TestCase
{
    public function testDeveCriarPedidoComSucesso()
    {
        $dataCriacao = new \DateTime();
        $pedido = new Pedido(
            'PEDI123',
            'CLI123',
            [],
            0,
            'CRIADO',
            $dataCriacao
        );

        $this->assertEquals('PEDI123', $pedido->getId());
        $this->assertEquals('CLI123', $pedido->getClienteId());
        $this->assertEquals([], $pedido->getItens());
        $this->assertEquals(0, $pedido->getValorTotal());
        $this->assertEquals('CRIADO', $pedido->getStatus());
        $this->assertEquals($dataCriacao, $pedido->getDataCriacao());
    }

    public function testDeveAdicionarItemERecalcularValorTotal()
    {
        $pedido = new Pedido(
            'PEDI123',
            'CLI123',
            [],
            0,
            'CRIADO',
            new \DateTime()
        );

        $item1 = new ItemPedido('PROD1', 2, 100.00);
        $item2 = new ItemPedido('PROD2', 1, 50.00);

        $pedido->adicionarItem($item1);
        $pedido->adicionarItem($item2);

        $this->assertCount(2, $pedido->getItens());
        $this->assertEquals(250.00, $pedido->getValorTotal());
    }

    public function testDeveAtualizarStatusDoPedido()
    {
        $pedido = new Pedido(
            'PEDI123',
            'CLI123',
            [],
            0,
            'CRIADO',
            new \DateTime()
        );

        $pedido->setStatus('EM_PREPARACAO');
        $this->assertEquals('EM_PREPARACAO', $pedido->getStatus());
    }
}
