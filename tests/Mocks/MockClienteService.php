<?php

namespace Tests\Mocks;

use App\Domain\Services\ClienteServiceInterface;

class MockClienteService implements ClienteServiceInterface
{
    private $clientesExistentes = [];

    public function setClienteExistente(string $clienteId, bool $existe): void
    {
        $this->clientesExistentes[$clienteId] = $existe;
    }

    public function clienteExiste(string $clienteId): bool
    {
        return $this->clientesExistentes[$clienteId] ?? false;
    }
}
