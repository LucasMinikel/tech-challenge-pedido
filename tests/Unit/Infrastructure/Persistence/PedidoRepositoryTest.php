<?php

namespace Tests\Unit\Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\PedidoRepository;
use App\Domain\Entities\Pedido;
use App\Domain\Entities\ItemPedido;
use PDO;
use PDOStatement;

class PedidoRepositoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PDO
     */
    private $pdo;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PDOStatement
     */
    private $pdoStatement;

    private $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->repository = new PedidoRepository($this->pdo);
    }

    public function testFindAll()
    {
        $pedidoData = [
            'id' => '123',
            'cliente_id' => '456',
            'valor_total' => 100.00,
            'status' => 'CRIADO',
            'data_criacao' => '2024-03-20 10:00:00'
        ];

        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $itemPedidoData = [
            'produto_id' => '789',
            'quantidade' => 2,
            'preco_unitario' => 50.00
        ];

        $stmtItens = $this->createMock(PDOStatement::class);
        $stmtItens->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls($itemPedidoData, false);

        $this->pdoStatement->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls($pedidoData, false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtItens);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Pedido::class, $result[0]);
    }

    public function testFindById()
    {
        $pedidoId = '123';
        $pedidoData = [
            'id' => $pedidoId,
            'cliente_id' => '456',
            'valor_total' => 100.00,
            'status' => 'CRIADO',
            'data_criacao' => '2024-03-20 10:00:00'
        ];

        $stmtPedido = $this->createMock(PDOStatement::class);
        $stmtPedido->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($pedidoData);

        $stmtItens = $this->createMock(PDOStatement::class);
        $stmtItens->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls(
                [
                    'produto_id' => '789',
                    'quantidade' => 2,
                    'preco_unitario' => 50.00
                ],
                false
            );

        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtPedido, $stmtItens);

        $result = $this->repository->findById($pedidoId);

        $this->assertInstanceOf(Pedido::class, $result);
        $this->assertEquals($pedidoId, $result->getId());
    }

    public function testSave()
    {
        $pedido = new Pedido(
            '123',
            '456',
            [new ItemPedido('789', 2, 50.00)],
            100.00,
            'CRIADO',
            new \DateTime()
        );

        $this->pdo->expects($this->once())
            ->method('beginTransaction');

        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdo->expects($this->once())
            ->method('commit');

        $result = $this->repository->save($pedido);

        $this->assertInstanceOf(Pedido::class, $result);
        $this->assertEquals($pedido->getId(), $result->getId());
    }

    public function testUpdate()
    {
        $pedido = new Pedido(
            '123',
            '456',
            [new ItemPedido('789', 2, 50.00)],
            100.00,
            'CONFIRMADO',
            new \DateTime()
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->repository->update($pedido);

        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $pedidoId = '123';

        $this->pdo->expects($this->once())
            ->method('beginTransaction');

        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        $this->pdo->expects($this->once())
            ->method('commit');

        $result = $this->repository->delete($pedidoId);

        $this->assertTrue($result);
    }

    public function testFindByClienteId()
    {
        $clienteId = '456';
        $pedidoData = [
            'id' => '123',
            'cliente_id' => $clienteId,
            'valor_total' => 100.00,
            'status' => 'CRIADO',
            'data_criacao' => '2024-03-20 10:00:00'
        ];

        $stmtPedido = $this->createMock(PDOStatement::class);
        $stmtPedido->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls($pedidoData, false);

        $stmtItens = $this->createMock(PDOStatement::class);
        $stmtItens->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls(
                [
                    'produto_id' => '789',
                    'quantidade' => 2,
                    'preco_unitario' => 50.00
                ],
                false
            );

        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtPedido, $stmtItens);

        $result = $this->repository->findByClienteId($clienteId);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Pedido::class, $result[0]);
        $this->assertEquals($clienteId, $result[0]->getClienteId());
    }
}
