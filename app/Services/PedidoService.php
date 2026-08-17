<?php

namespace App\Services;

use App\Models\Pedido;
use App\Repositories\PedidoRepository;
use Exception;

class PedidoService
{
    private PedidoRepository $repository;

    public function __construct(PedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function guardar(Pedido $pedido): void
    {
        $this->validarPedido($pedido);

        $this->repository->guardar($pedido);
    }

    public function obtenerPorId(int $id): Pedido
    {
        $pedido = $this->repository->obtenerPorId($id);

        if ($pedido === null) {
            throw new Exception("El pedido no existe.");
        }

        return $pedido;
    }

    public function actualizar(Pedido $pedido): void
    {
        $this->obtenerPorId($pedido->getId());

        $this->validarPedido($pedido);

        $this->repository->actualizar($pedido);
    }

    public function eliminar(int $id): void
    {
        $this->obtenerPorId($id);

        $this->repository->eliminar($id);
    }

    private function validarPedido(Pedido $pedido): void
    {
        if (trim($pedido->getEstado()) === "") {
            throw new Exception("El estado es obligatorio");
        }

        if ($pedido->getTotal() < 0) {
            throw new Exception("El total no puede ser negativo");
        }
    }
}