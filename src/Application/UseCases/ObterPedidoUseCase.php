<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class ObterPedidoUseCase
{
    public function __construct(
        private PedidoRepositoryInterface $pedidoRepository
    ) {}

    public function execute(string $pedidoId): PedidoDTO
    {
        $pedido = $this->pedidoRepository->findById($pedidoId);
        if (!$pedido) {
            throw new \Exception("Pedido não encontrado");
        }

        return PedidoDTO::fromEntity($pedido);
    }
}
