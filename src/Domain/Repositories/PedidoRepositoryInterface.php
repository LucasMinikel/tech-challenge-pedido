<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Pedido;

interface PedidoRepositoryInterface
{
    public function save(Pedido $pedido): Pedido;
    public function findById(string $id): ?Pedido;
    public function findAll(): array;
    public function findByClienteId(string $clienteId): array;
    public function update(Pedido $pedido): bool;
    public function delete(string $id): bool;
}
