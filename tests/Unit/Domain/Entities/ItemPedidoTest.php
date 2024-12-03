<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use App\Domain\Entities\ItemPedido;

class ItemPedidoTest extends TestCase
{
    public function testDeveCriarItemPedidoComSucesso()
    {
        $item = new ItemPedido('PROD123', 2, 100.00);

        $this->assertEquals('PROD123', $item->getProdutoId());
        $this->assertEquals(2, $item->getQuantidade());
        $this->assertEquals(100.00, $item->getPrecoUnitario());
    }
}
