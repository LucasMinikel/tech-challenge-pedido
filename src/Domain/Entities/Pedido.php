<?php

namespace App\Domain\Entities;

class Pedido
{
    public function __construct(
        private ?string $id,
        private string $clienteId,
        private array $itens,
        private float $valorTotal,
        private string $status,
        private \DateTime $dataCriacao
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getItens(): array
    {
        return $this->itens;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDataCriacao(): \DateTime
    {
        return $this->dataCriacao;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function adicionarItem(ItemPedido $item): void
    {
        $this->itens[] = $item;
        $this->recalcularValorTotal();
    }

    private function recalcularValorTotal(): void
    {
        $this->valorTotal = array_reduce($this->itens, function ($total, $item) {
            return $total + ($item->getPrecoUnitario() * $item->getQuantidade());
        }, 0);
    }
}
