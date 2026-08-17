<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Pedido;
use DateTime;

class PedidoRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function guardar(Pedido $pedido): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "INSERT INTO pedidos
                (
                    id_cliente,
                    fechaPedido,
                    estado,
                    total
                )
                VALUES
                (
                    :idCliente,
                    :fechaPedido,
                    :estado,
                    :total
                )";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idCliente" => $pedido->getIdCliente(),
            "fechaPedido" => $pedido->getFechaPedido()->format("Y-m-d"),
            "estado" => $pedido->getEstado(),
            "total" => $pedido->getTotal()
        ]);
    }

    public function obtenerTodos(): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM pedidos
                WHERE deleted_at IS NULL";

        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $pedidos = [];

        foreach ($filas as $fila) {
            $pedido = $this->convertirEnPedido($fila);

            $pedidos[] = $pedido;
        }

        return $pedidos;
    }

    public function obtenerPorId(int $id): ?Pedido
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM pedidos
                WHERE id = :id
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $id
        ]);

        $fila = $sentencia->fetch();

        if ($fila === false) {
            return null;
        }

        return $this->convertirEnPedido($fila);
    }

    public function actualizar(Pedido $pedido): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE pedidos
                SET
                    id_cliente = :idCliente,
                    fechaPedido = :fechaPedido,
                    estado = :estado,
                    total = :total
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $pedido->getId(),
            "idCliente" => $pedido->getIdCliente(),
            "fechaPedido" => $pedido->getFechaPedido()->format("Y-m-d"),
            "estado" => $pedido->getEstado(),
            "total" => $pedido->getTotal()
        ]);
    }

    public function eliminar(int $id): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE pedidos
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $id
        ]);
    }

    private function convertirEnPedido(array $fila): Pedido
    {
        return new Pedido(
            $fila["id"],
            $fila["id_cliente"],
            new DateTime($fila["fechaPedido"]),
            $fila["estado"],
            (float) $fila["total"]
        );
    }

    public function existePedidoPorId(int $id): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id
                FROM pedidos
                WHERE id = :id
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute(["id" => $id]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }
}