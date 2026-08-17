<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\DetallePedido;

class DetallePedidoRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function guardar(DetallePedido $detallePedido): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "INSERT INTO detalle_pedidos
                (
                    id_pedido,
                    id_producto,
                    cantidad,
                    precio,
                    subtotal
                )
                VALUES
                (
                    :idPedido,
                    :idProducto,
                    :cantidad,
                    :precio,
                    :subtotal
                )";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idPedido" => $detallePedido->getIdPedido(),
            "idProducto" => $detallePedido->getIdProducto(),
            "cantidad" => $detallePedido->getCantidad(),
            "precio" => $detallePedido->getPrecioUnitario(),
            "subtotal" => $detallePedido->getSubtotal()
        ]);
    }

    public function obtenerTodos(): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM detalle_pedidos
                WHERE deleted_at IS NULL";

        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $detalles = [];

        foreach ($filas as $fila) {
            $detalle = $this->convertirEnDetallePedido($fila);

            $detalles[] = $detalle;
        }

        return $detalles;
    }

    public function obtenerPorId(int $id): ?DetallePedido
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM detalle_pedidos
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

        return $this->convertirEnDetallePedido($fila);
    }

    public function obtenerPorPedido(int $idPedido): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM detalle_pedidos
                WHERE id_pedido = :idPedido
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idPedido" => $idPedido
        ]);

        $filas = $sentencia->fetchAll();

        $detalles = [];

        foreach ($filas as $fila) {
            $detalle = $this->convertirEnDetallePedido($fila);

            $detalles[] = $detalle;
        }

        return $detalles;
    }

    public function actualizar(DetallePedido $detallePedido): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE detalle_pedidos
                SET
                    id_pedido = :idPedido,
                    id_producto = :idProducto,
                    cantidad = :cantidad,
                    precio = :precio,
                    subtotal = :subtotal
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $detallePedido->getId(),
            "idPedido" => $detallePedido->getIdPedido(),
            "idProducto" => $detallePedido->getIdProducto(),
            "cantidad" => $detallePedido->getCantidad(),
            "precio" => $detallePedido->getPrecioUnitario(),
            "subtotal" => $detallePedido->getSubtotal()
        ]);
    }

    public function eliminar(int $id): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE detalle_pedidos
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $id
        ]);
    }

    private function convertirEnDetallePedido(array $fila): DetallePedido
    {
        return new DetallePedido(
            $fila["id"],
            $fila["id_pedido"],
            $fila["id_producto"],
            $fila["cantidad"],
            (float) $fila["precio"],
            (float) $fila["subtotal"]
        );
    }

    public function existeDetallePorProducto(int $idPedido, int $idProducto): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id
                FROM detalle_pedidos
                WHERE id_pedido = :idPedido
                AND id_producto = :idProducto
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto
        ]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }

    public function existeDetallePorProductoExceptoId(int $idPedido, int $idProducto, int $id): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id
                FROM detalle_pedidos
                WHERE id_pedido = :idPedido
                AND id_producto = :idProducto
                AND id <> :id
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto,
            "id" => $id
        ]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }
}