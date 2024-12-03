<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Application\DTOs\PedidoDTO;

class AtualizarStatusPedidoUseCase
{
    public function __construct(
        private PedidoRepositoryInterface $pedidoRepository
    ) {}

    public function execute(string $pedidoId, string $novoStatus): PedidoDTO
    {
        $pedido = $this->pedidoRepository->findById($pedidoId);
        if (!$pedido) {
            throw new \Exception("Pedido não encontrado");
        }

        $pedido->setStatus($novoStatus);
        $this->pedidoRepository->update($pedido);

        return PedidoDTO::fromEntity($pedido);
    }
}
