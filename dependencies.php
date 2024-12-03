<?php

use DI\ContainerBuilder;
use App\Domain\Repositories\PedidoRepositoryInterface;
use App\Domain\Services\CatalogoServiceInterface;
use App\Domain\Services\ClienteServiceInterface;
use App\Infrastructure\Persistence\PedidoRepository;
use App\Infrastructure\Services\HttpCatalogoService;
use App\Infrastructure\Services\HttpClienteService;
use App\Application\UseCases\CriarPedidoUseCase;
use App\Application\UseCases\AtualizarStatusPedidoUseCase;
use App\Application\UseCases\ListarPedidosUseCase;
use App\Application\UseCases\ObterPedidoUseCase;
use App\Infrastructure\API\Controllers\PedidoController;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function () {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $dbname = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        },

        PedidoRepositoryInterface::class => \DI\autowire(PedidoRepository::class),
        ClienteServiceInterface::class => function () {
            return new HttpClienteService($_ENV['CLIENTE_SERVICE_URL']);
        },
        CatalogoServiceInterface::class => function () {
            return new HttpCatalogoService($_ENV['CATALOGO_SERVICE_URL']);
        },

        CriarPedidoUseCase::class => \DI\autowire(),
        AtualizarStatusPedidoUseCase::class => \DI\autowire(),
        ListarPedidosUseCase::class => \DI\autowire(),
        ObterPedidoUseCase::class => \DI\autowire(),

        PedidoController::class => \DI\autowire()
    ]);
};
