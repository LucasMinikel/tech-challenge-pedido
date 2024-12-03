<?php

namespace App\Domain\Interfaces;

interface ClienteServiceInterface
{
    public function clienteExiste(string $clienteId): bool;
}
