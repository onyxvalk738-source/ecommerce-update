<?php

namespace App\Services;

use App\Models\Pedido;
use App\Repositories\DetallePedidoRepository;
use App\Repositories\PedidoRepository;
use Exception;

class PedidoService
{
    private PedidoRepository $repository;
    private DetallePedidoRepository $detalleRepository;

    private const TRANSICIONES_ESTADO = [
        Pedido::ESTADO_PENDIENTE  => [Pedido::ESTADO_PROCESADO, Pedido::ESTADO_CANCELADO],
        Pedido::ESTADO_PROCESADO  => [Pedido::ESTADO_ENVIADO, Pedido::ESTADO_CANCELADO],
        Pedido::ESTADO_ENVIADO    => [Pedido::ESTADO_ENTREGADO, Pedido::ESTADO_CANCELADO],
        Pedido::ESTADO_ENTREGADO  => [],
        Pedido::ESTADO_CANCELADO  => []
    ];

    public function __construct(PedidoRepository $repository, DetallePedidoRepository $detalleRepository)
    {
        $this->repository = $repository;
        $this->detalleRepository = $detalleRepository;
    }

    public function guardar(Pedido $pedido): void
    {
        $this->validarPedido($pedido);

        if (trim($pedido->getEstado()) === "") {
            $pedido->setEstado(Pedido::ESTADO_PENDIENTE);
        }

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

        if (trim($pedido->getEstado()) === "") {
            $pedido->setEstado(Pedido::ESTADO_PENDIENTE);
        }

        $this->validarPedido($pedido);

        $this->repository->actualizar($pedido);
    }

    public function cambiarEstado(int $id, string $estadoNuevo): void
    {
        $pedido = $this->obtenerPorId($id);

        $transicionesPermitidas = self::TRANSICIONES_ESTADO[$pedido->getEstado()] ?? [];

        if (!in_array($estadoNuevo, $transicionesPermitidas, true)) {
            throw new Exception(
                "No se puede cambiar el estado de \""
                . $pedido->getEstado()
                . "\" a \""
                . $estadoNuevo
                . "\""
            );
        }

        $this->repository->actualizarEstado($id, $estadoNuevo);
    }

    public function recalcularTotal(int $id): void
    {
        $this->obtenerPorId($id);

        $detalles = $this->detalleRepository->obtenerPorPedido($id);

        $total = 0.0;

        foreach ($detalles as $detalle) {
            $total += $detalle->getSubtotal();
        }

        $this->repository->actualizarTotal($id, $total);
    }

    public function eliminar(int $id): void
    {
        $this->obtenerPorId($id);

        $this->repository->eliminar($id);
    }

    private function validarPedido(Pedido $pedido): void
    {
        $estado = $pedido->getEstado();

        if ($estado !== "" && !in_array($estado, self::TRANSICIONES_ESTADO, true)) {
            throw new Exception("Estado de pedido inválido");
        }

        if ($pedido->getTotal() < 0) {
            throw new Exception("El total no puede ser negativo");
        }
    }
}