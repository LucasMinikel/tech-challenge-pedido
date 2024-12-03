<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class ListarPedidosUseCase
{
    public function __construct(
        private PedidoRepositoryInterface $pedidoRepository
    ) {}

    public function execute(): array
    {
        $pedidos = $this->pedidoRepository->findAll();
        return array_map(fn($pedido) => PedidoDTO::fromEntity($pedido), $pedidos);
    }
}
