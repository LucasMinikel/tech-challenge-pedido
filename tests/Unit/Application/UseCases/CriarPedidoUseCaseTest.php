<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\CriarPedidoUseCase;
use App\Application\DTOs\PedidoDTO;
use App\Domain\Entities\Pedido;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Domain\Services\CatalogoServiceInterface;
use App\Domain\Services\ClienteServiceInterface;

class CriarPedidoUseCaseTest extends TestCase
{
    /** @var PedidoRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $pedidoRepository;

    /** @var CatalogoServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $catalogoService;

    /** @var ClienteServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $clienteService;

    private $criarPedidoUseCase;

    protected function setUp(): void
    {
        $this->pedidoRepository = $this->createMock(PedidoRepositoryInterface::class);
        $this->catalogoService = $this->createMock(CatalogoServiceInterface::class);
        $this->clienteService = $this->createMock(ClienteServiceInterface::class);

        $this->criarPedidoUseCase = new CriarPedidoUseCase(
            $this->pedidoRepository,
            $this->catalogoService,
            $this->clienteService
        );
    }

    public function testDeveCriarPedidoComSucesso()
    {
        $pedidoDTO = new PedidoDTO(
            null,
            '1',
            [
                ['produtoId' => '1', 'quantidade' => 2],
                ['produtoId' => '2', 'quantidade' => 1]
            ],
            0,
            'CRIADO',
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->clienteService
            ->expects($this->once())
            ->method('clienteExiste')
            ->with('1')
            ->willReturn(true);

        $this->catalogoService
            ->expects($this->exactly(2))
            ->method('obterProduto')
            ->willReturnMap([
                ['1', ['id' => '1', 'preco' => 100.00]],
                ['2', ['id' => '2', 'preco' => 50.00]]
            ]);

        $this->pedidoRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Pedido::class))
            ->willReturnCallback(function ($pedido) {
                return $pedido;
            });

        $resultado = $this->criarPedidoUseCase->execute($pedidoDTO);

        $this->assertInstanceOf(PedidoDTO::class, $resultado);
        $this->assertEquals('1', $resultado->clienteId);
        $this->assertEquals(250.00, $resultado->valorTotal);
    }

    public function testDeveLancarExcecaoQuandoClienteNaoExiste()
    {
        $pedidoDTO = new PedidoDTO(
            null,
            '999',
            [['produtoId' => '1', 'quantidade' => 2]],
            0,
            'CRIADO',
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->clienteService
            ->expects($this->once())
            ->method('clienteExiste')
            ->with('999')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cliente não encontrado');

        $this->criarPedidoUseCase->execute($pedidoDTO);
    }

    public function testDeveLancarExcecaoQuandoProdutoNaoExiste()
    {
        $pedidoDTO = new PedidoDTO(
            null,
            '1',
            [['produtoId' => '999', 'quantidade' => 1]],
            0,
            'CRIADO',
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->clienteService
            ->expects($this->once())
            ->method('clienteExiste')
            ->with('1')
            ->willReturn(true);

        $this->catalogoService
            ->expects($this->once())
            ->method('obterProduto')
            ->with('999')
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Produto não encontrado: 999');

        $this->criarPedidoUseCase->execute($pedidoDTO);
    }
}
