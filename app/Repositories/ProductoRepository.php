<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Producto;
use DateTime;

class ProductoRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function guardar(Producto $producto): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "INSERT INTO productos
                (
                    id_categoria,
                    nombre,
                    fechaVencimiento,
                    informacion,
                    codigo,
                    precio,
                    unidades,
                    estado
                )
                VALUES
                (
                    :idCategoria,
                    :nombre,
                    :fechaVencimiento,
                    :informacion,
                    :codigo,
                    :precio,
                    :unidades,
                    :estado
                )";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "idCategoria" => $producto->getIdCategoria(),
            "nombre" => $producto->getNombre(),
            "fechaVencimiento" => $producto->getFechaVencimiento()->format("Y-m-d"),
            "informacion" => $producto->getInformacion(),
            "codigo" => $producto->getCodigo(),
            "precio" => $producto->getPrecio(),
            "unidades" => $producto->getUnidades(),
            "estado" => $producto->getEstado()
        ]);
    }

    public function obtenerTodos(): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM productos
                WHERE deleted_at IS NULL";

        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $productos = [];

        foreach ($filas as $fila) {
            $producto = $this->convertirEnProducto($fila);

            $productos[] = $producto;
        }

        return $productos;
    }

    public function obtenerPorId(int $id): ?Producto
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM productos
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

        return $this->convertirEnProducto($fila);
    }

    public function actualizar(Producto $producto): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE productos
                SET
                    id_categoria = :idCategoria,
                    nombre = :nombre,
                    fechaVencimiento = :fechaVencimiento,
                    informacion = :informacion,
                    codigo = :codigo,
                    precio = :precio,
                    unidades = :unidades,
                    estado = :estado
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $producto->getId(),
            "idCategoria" => $producto->getIdCategoria(),
            "nombre" => $producto->getNombre(),
            "fechaVencimiento" => $producto->getFechaVencimiento()->format("Y-m-d"),
            "informacion" => $producto->getInformacion(),
            "codigo" => $producto->getCodigo(),
            "precio" => $producto->getPrecio(),
            "unidades" => $producto->getUnidades(),
            "estado" => $producto->getEstado()
        ]);
    }


    public function eliminar(int $id): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE productos
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

         $sentencia->execute([
            "id" => $id
        ]);
    }

     private function convertirEnProducto(array $fila): Producto
    {
        return new Producto(
            $fila["id"],
            $fila["id_categoria"],
            $fila["nombre"],
            new DateTime($fila["fechaVencimiento"]),
            $fila["informacion"],
            $fila["codigo"],
            (float) $fila["precio"],
            $fila["unidades"],
            (bool) $fila["estado"]
        );
    }

    public function existeCodigo(string $codigo): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id 
                FROM productos
                WHERE codigo = :codigo
                AND deleted_at IS NULL";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute(["codigo" => $codigo]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }

    public function existeCodigoExceptoId(string $codigo, int $id): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id 
                FROM productos
                WHERE codigo = :codigo
                AND id <> :id
                AND deleted_at IS NULL";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute(["codigo" => $codigo, "id" => $id
        ]);

        $fila = $sentencia->fetch();

        return $fila !== false;


    }

}