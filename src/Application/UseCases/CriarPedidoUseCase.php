<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Pedido;
use App\Domain\Entities\ItemPedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Domain\Services\CatalogoServiceInterface;
use App\Domain\Services\ClienteServiceInterface;
use App\Application\DTOs\PedidoDTO;

class CriarPedidoUseCase
{
    public function __construct(
        private PedidoRepositoryInterface $pedidoRepository,
        private CatalogoServiceInterface $catalogoService,
        private ClienteServiceInterface $clienteService
    ) {}

    public function execute(PedidoDTO $pedidoDTO): PedidoDTO
    {
        if (!$this->clienteService->clienteExiste($pedidoDTO->clienteId)) {
            throw new \Exception("Cliente não encontrado");
        }

        $itens = [];
        foreach ($pedidoDTO->itens as $item) {
            $produto = $this->catalogoService->obterProduto($item['produtoId']);
            if (!$produto) {
                throw new \Exception("Produto não encontrado: " . $item['produtoId']);
            }
            $itens[] = new ItemPedido($item['produtoId'], $item['quantidade'], $produto['preco']);
        }

        $id = 'PEDI' . uniqid();

        $pedido = new Pedido(
            $id,
            $pedidoDTO->clienteId,
            [],
            0,
            'CRIADO',
            new \DateTime()
        );

        foreach ($itens as $item) {
            $pedido->adicionarItem($item);
        }

        $this->pedidoRepository->save($pedido);

        return PedidoDTO::fromEntity($pedido);
    }
}
