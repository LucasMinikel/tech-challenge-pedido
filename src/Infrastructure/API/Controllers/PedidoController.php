<?php

namespace App\Infrastructure\API\Controllers;

use App\Application\UseCases\CriarPedidoUseCase;
use App\Application\UseCases\AtualizarStatusPedidoUseCase;
use App\Application\UseCases\ListarPedidosUseCase;
use App\Application\UseCases\ObterPedidoUseCase;
use App\Application\DTOs\PedidoDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoController
{
    public function __construct(
        private CriarPedidoUseCase $criarPedidoUseCase,
        private AtualizarStatusPedidoUseCase $atualizarStatusPedidoUseCase,
        private ListarPedidosUseCase $listarPedidosUseCase,
        private ObterPedidoUseCase $obterPedidoUseCase
    ) {}

    public function criar(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $pedidoDTO = new PedidoDTO(
                null,
                $data['clienteId'],
                $data['itens'],
                0,
                'CRIADO',
                (new \DateTime())->format('Y-m-d H:i:s')
            );

            $pedidoCriado = $this->criarPedidoUseCase->execute($pedidoDTO);
            $response->getBody()->write(json_encode($pedidoCriado->toArray()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function atualizarStatus(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $pedidoAtualizado = $this->atualizarStatusPedidoUseCase->execute($id, $data['status']);
            $response->getBody()->write(json_encode($pedidoAtualizado->toArray()));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function listar(Request $request, Response $response): Response
    {
        $pedidos = $this->listarPedidosUseCase->execute();
        $response->getBody()->write(json_encode(array_map(fn($pedido) => $pedido->toArray(), $pedidos)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obter(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        try {
            $pedido = $this->obterPedidoUseCase->execute($id);
            $response->getBody()->write(json_encode($pedido->toArray()));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}
