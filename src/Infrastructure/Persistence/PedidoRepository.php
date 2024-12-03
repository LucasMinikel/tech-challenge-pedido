<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Pedido;
use App\Domain\Entities\ItemPedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use PDO;

class PedidoRepository implements PedidoRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM pedidos');
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->hydratePedido($row);
        }
        return $pedidos;
    }

    public function findById(string $id): ?Pedido
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pedidos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydratePedido($row);
    }

    public function save(Pedido $pedido): Pedido
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('INSERT INTO pedidos (id, cliente_id, valor_total, status, data_criacao) VALUES (:id, :cliente_id, :valor_total, :status, :data_criacao)');
            $stmt->execute([
                'id' => $pedido->getId(),
                'cliente_id' => $pedido->getClienteId(),
                'valor_total' => $pedido->getValorTotal(),
                'status' => $pedido->getStatus(),
                'data_criacao' => $pedido->getDataCriacao()->format('Y-m-d H:i:s')
            ]);

            foreach ($pedido->getItens() as $item) {
                $this->saveItemPedido($pedido->getId(), $item);
            }

            $this->pdo->commit();
            return $pedido;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Pedido $pedido): bool
    {
        $stmt = $this->pdo->prepare('UPDATE pedidos SET cliente_id = :cliente_id, valor_total = :valor_total, status = :status WHERE id = :id');
        return $stmt->execute([
            'id' => $pedido->getId(),
            'cliente_id' => $pedido->getClienteId(),
            'valor_total' => $pedido->getValorTotal(),
            'status' => $pedido->getStatus()
        ]);
    }

    public function delete(string $id): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('DELETE FROM itens_pedido WHERE pedido_id = :id');
            $stmt->execute(['id' => $id]);

            $stmt = $this->pdo->prepare('DELETE FROM pedidos WHERE id = :id');
            $result = $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findByClienteId(string $clienteId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pedidos WHERE cliente_id = :cliente_id');
        $stmt->execute(['cliente_id' => $clienteId]);
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->hydratePedido($row);
        }
        return $pedidos;
    }

    private function hydratePedido(array $row): Pedido
    {
        $pedido = new Pedido(
            $row['id'],
            $row['cliente_id'],
            $this->getItensPedido($row['id']),
            $row['valor_total'],
            $row['status'],
            new \DateTime($row['data_criacao'])
        );
        return $pedido;
    }

    private function getItensPedido(string $pedidoId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM itens_pedido WHERE pedido_id = :pedido_id');
        $stmt->execute(['pedido_id' => $pedidoId]);
        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new ItemPedido(
                $row['produto_id'],
                $row['quantidade'],
                $row['preco_unitario']
            );
        }
        return $itens;
    }

    private function saveItemPedido(string $pedidoId, ItemPedido $item): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)');
        $stmt->execute([
            'pedido_id' => $pedidoId,
            'produto_id' => $item->getProdutoId(),
            'quantidade' => $item->getQuantidade(),
            'preco_unitario' => $item->getPrecoUnitario()
        ]);
    }
}
