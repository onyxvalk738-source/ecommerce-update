<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Repositories\DetallePedidoRepository;
use Exception;

class DetallePedidoService
{
    private DetallePedidoRepository $repository;

    public function __construct(DetallePedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function guardar(DetallePedido $detallePedido): void
    {
        $this->validarDetallePedido($detallePedido);

        if ($this->repository->existeDetallePorProducto(
            $detallePedido->getIdPedido(),
            $detallePedido->getIdProducto()
        )) {
            throw new Exception("Ya existe un detalle con este producto en el pedido");
        }

        $this->repository->guardar($detallePedido);
    }

    public function obtenerPorId(int $id): DetallePedido
    {
        $detallePedido = $this->repository->obtenerPorId($id);

        if ($detallePedido === null) {
            throw new Exception("El detalle del pedido no existe.");
        }

        return $detallePedido;
    }

    public function obtenerPorPedido(int $idPedido): array
    {
        return $this->repository->obtenerPorPedido($idPedido);
    }

    public function actualizar(DetallePedido $detallePedido): void
    {
        $this->obtenerPorId($detallePedido->getId());

        $this->validarDetallePedido($detallePedido);

        if ($this->repository->existeDetallePorProductoExceptoId(
            $detallePedido->getIdPedido(),
            $detallePedido->getIdProducto(),
            $detallePedido->getId()
        )) {
            throw new Exception("Ya existe un detalle con este producto en el pedido");
        }

        $this->repository->actualizar($detallePedido);
    }

    public function eliminar(int $id): void
    {
        $this->obtenerPorId($id);

        $this->repository->eliminar($id);
    }

    private function validarDetallePedido(DetallePedido $detallePedido): void
    {
        if ($detallePedido->getIdPedido() === null) {
            throw new Exception("El pedido es obligatorio");
        }

        if ($detallePedido->getIdProducto() === null) {
            throw new Exception("El producto es obligatorio");
        }

        if ($detallePedido->getCantidad() <= 0) {
            throw new Exception("La cantidad debe ser mayor que cero");
        }

        if ($detallePedido->getPrecioUnitario() <= 0) {
            throw new Exception("El precio debe ser mayor que cero");
        }

        if ($detallePedido->getSubtotal() < 0) {
            throw new Exception("El subtotal no puede ser negativo");
        }
    }
}