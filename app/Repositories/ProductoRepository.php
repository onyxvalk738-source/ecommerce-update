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

        $sql = "SELECT * FROM productos";

        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $productos = [];

        foreach ($filas as $fila){
            $producto = $this->convertirEnProducto($fila);

            $productos[] = $producto;
        }
        return $productos;
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

    public function obtenerPorId(int $id): ?Producto
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM productos
               WHERE id = :id";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute(["id" => $id]);

        $fila = $sentencia->fetch();

        if ($fila === false)
            {
                return null;
            }

            return $this->convertirEnProducto($fila);
    }
}
    
