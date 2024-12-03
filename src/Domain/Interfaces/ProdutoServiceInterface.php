<?php

namespace App\Domain\Interfaces;

interface ProdutoServiceInterface
{
    public function obterProduto(string $clienteId): bool;
}
