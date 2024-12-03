<?php

namespace App\Domain\Services;

interface CatalogoServiceInterface
{
    public function obterProduto(string $produtoId): ?array;
}
