<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Repositories\DetallePedidoRepository;
use App\Repositories\ProductoRepository;
use Exception;

class DetallePedidoService
{
    private DetallePedidoRepository $repository;
    private ProductoRepository $productoRepository;
    private PedidoService $pedidoService;

    public function __construct(
        DetallePedidoRepository $repository,
        ProductoRepository $productoRepository,
        PedidoService $pedidoService
    ) {
        $this->repository = $repository;
        $this->productoRepository = $productoRepository;
        $this->pedidoService = $pedidoService;
    }

    public function guardar(DetallePedido $detallePedido): void
    {
        $this->validarDetallePedido($detallePedido);

        $this->recalcularSubtotal($detallePedido);

        if ($this->repository->existeDetallePorProducto(
            $detallePedido->getIdPedido(),
            $detallePedido->getIdProducto()
        )) {
            throw new Exception("Ya existe un detalle con este producto en el pedido");
        }

        $this->repository->guardar($detallePedido);

        $this->pedidoService->recalcularTotal($detallePedido->getIdPedido());
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

        $this->recalcularSubtotal($detallePedido);

        if ($this->repository->existeDetallePorProductoExceptoId(
            $detallePedido->getIdPedido(),
            $detallePedido->getIdProducto(),
            $detallePedido->getId()
        )) {
            throw new Exception("Ya existe un detalle con este producto en el pedido");
        }

        $this->repository->actualizar($detallePedido);

        $this->pedidoService->recalcularTotal($detallePedido->getIdPedido());
    }

    public function eliminar(int $id): void
    {
        $detallePedido = $this->obtenerPorId($id);

        $this->repository->eliminar($id);

        $this->pedidoService->recalcularTotal($detallePedido->getIdPedido());
    }

    private function recalcularSubtotal(DetallePedido $detallePedido): void
    {
        $detallePedido->setSubtotal(
            $detallePedido->getCantidad() * $detallePedido->getPrecioUnitario()
        );
    }

    private function validarDetallePedido(DetallePedido $detallePedido): void
    {
        if ($detallePedido->getIdPedido() === null) {
            throw new Exception("El pedido es obligatorio");
        }

        $this->pedidoService->obtenerPorId($detallePedido->getIdPedido());

        if ($detallePedido->getIdProducto() === null) {
            throw new Exception("El producto es obligatorio");
        }

        if ($this->productoRepository->obtenerPorId($detallePedido->getIdProducto()) === null) {
            throw new Exception("El producto no existe");
        }

        if ($detallePedido->getCantidad() <= 0) {
            throw new Exception("La cantidad debe ser mayor que cero");
        }

        if ($detallePedido->getPrecioUnitario() <= 0) {
            throw new Exception("El precio debe ser mayor que cero");
        }
    }
}