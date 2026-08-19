<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Exception;

class ClienteService 
{
    private ClienteRepository $clienteRepository;


    public function __construct(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function validarCliente(Cliente $cliente): void
    {
        if(trim($cliente->getNombre())=== "") {
            throw new Exception("El nombre es obligatorio");
        }
    }
}