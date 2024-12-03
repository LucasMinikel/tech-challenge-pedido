<?php

namespace App\Domain\Entities;

class ItemPedido
{
    public function __construct(
        private string $produtoId,
        private int $quantidade,
        private float $precoUnitario
    ) {}

    public function getProdutoId(): string
    {
        return $this->produtoId;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getPrecoUnitario(): float
    {
        return $this->precoUnitario;
    }
}
