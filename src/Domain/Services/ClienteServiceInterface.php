<?php

namespace App\Domain\Services;

interface ClienteServiceInterface
{
    public function clienteExiste(string $clienteId): bool;
}
