<?php

namespace Tests\Unit\Application\DTOs;

use PHPUnit\Framework\TestCase;
use App\Application\DTOs\PedidoDTO;

class PedidoDTOTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        $this->testData = [
            'id' => '123',
            'clienteId' => '456',
            'itens' => [
                [
                    'produtoId' => '789',
                    'quantidade' => 2,
                    'precoUnitario' => 50.00
                ]
            ],
            'valorTotal' => 100.00,
            'status' => 'CRIADO',
            'dataCriacao' => '2024-03-20 10:00:00'
        ];
    }

    public function testCriarPedidoDTO()
    {
        $pedidoDTO = new PedidoDTO(
            $this->testData['id'],
            $this->testData['clienteId'],
            $this->testData['itens'],
            $this->testData['valorTotal'],
            $this->testData['status'],
            $this->testData['dataCriacao']
        );

        $this->assertEquals($this->testData['id'], $pedidoDTO->id);
        $this->assertEquals($this->testData['clienteId'], $pedidoDTO->clienteId);
        $this->assertEquals($this->testData['valorTotal'], $pedidoDTO->valorTotal);
        $this->assertEquals($this->testData['status'], $pedidoDTO->status);
        $this->assertEquals($this->testData['dataCriacao'], $pedidoDTO->dataCriacao);

        $this->assertIsArray($pedidoDTO->itens);
        $this->assertCount(1, $pedidoDTO->itens);
        $this->assertEquals($this->testData['itens'][0]['produtoId'], $pedidoDTO->itens[0]['produtoId']);
        $this->assertEquals($this->testData['itens'][0]['quantidade'], $pedidoDTO->itens[0]['quantidade']);
        $this->assertEquals($this->testData['itens'][0]['precoUnitario'], $pedidoDTO->itens[0]['precoUnitario']);
    }

    public function testToArray()
    {
        $pedidoDTO = new PedidoDTO(
            $this->testData['id'],
            $this->testData['clienteId'],
            $this->testData['itens'],
            $this->testData['valorTotal'],
            $this->testData['status'],
            $this->testData['dataCriacao']
        );

        $array = $pedidoDTO->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($this->testData['id'], $array['id']);
        $this->assertEquals($this->testData['clienteId'], $array['clienteId']);
        $this->assertEquals($this->testData['itens'], $array['itens']);
        $this->assertEquals($this->testData['valorTotal'], $array['valorTotal']);
        $this->assertEquals($this->testData['status'], $array['status']);
        $this->assertEquals($this->testData['dataCriacao'], $array['dataCriacao']);
    }

    public function testCriarPedidoDTOSemId()
    {
        $pedidoDTO = new PedidoDTO(
            null,
            $this->testData['clienteId'],
            $this->testData['itens'],
            $this->testData['valorTotal'],
            $this->testData['status'],
            $this->testData['dataCriacao']
        );

        $this->assertNull($pedidoDTO->id);
        $this->assertEquals($this->testData['clienteId'], $pedidoDTO->clienteId);
    }
}
