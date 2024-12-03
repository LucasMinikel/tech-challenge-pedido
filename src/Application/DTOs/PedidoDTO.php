<?php

namespace App\Application\DTOs;

use App\Domain\Entities\Pedido;

class PedidoDTO
{
    public function __construct(
        public ?string $id,
        public string $clienteId,
        public array $itens,
        public float $valorTotal,
        public string $status,
        public string $dataCriacao
    ) {}

    public static function fromEntity(Pedido $pedido): self
    {
        return new self(
            $pedido->getId(),
            $pedido->getClienteId(),
            array_map(fn($item) => [
                'produtoId' => $item->getProdutoId(),
                'quantidade' => $item->getQuantidade(),
                'precoUnitario' => $item->getPrecoUnitario()
            ], $pedido->getItens()),
            $pedido->getValorTotal(),
            $pedido->getStatus(),
            $pedido->getDataCriacao()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->clienteId,
            'itens' => $this->itens,
            'valorTotal' => $this->valorTotal,
            'status' => $this->status,
            'dataCriacao' => $this->dataCriacao
        ];
    }
}
