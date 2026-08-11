<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Categoria;


class CategoriaRepository{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function guardar(Categoria $categoria): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "INSERT INTO categorias
        (
                nombreCategoria,
                detalleCategoria
        )
        VALUES
        (      
                :nombreCategoria,
                :detalleCategoria
        )";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "nombreCategoria" => $categoria->getNombreCategoria(),
            "detalleCategoria" => $categoria->getDetalleCategoria()
        ]);
    }

    public function obtenerTodos(): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM categorias
                WHERE deleted_at IS NULL";
        
        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $categorias = [];

        foreach ($filas as $fila) {
            $categoria = $this->convertirEnCategoria($fila);

            $categorias[] = $categoria;
        }
        return $categorias;
    }

    public function obtenerPorId(int $id): ?Categoria
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM categorias
                WHERE id = :id
                AND deleted_at IS NULL";
        
        $sentencia = $pdo->prepare($sql);
        
        $sentencia->execute(["id" => $id]);

        $fila = $sentencia->fetch();

        if ($fila === false) {
            return null;
        }

        return $this->convertirEnCategoria($fila);
    }

    public function actualizar(Categoria $categoria) 
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE categorias
                SET 
                    nombreCategoria = :nombreCategoria,
                    detalleCategoria = :detalleCategoria
                WHERE id = :id";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $categoria->getId(),
            "nombreCategoria" => $categoria->getNombreCategoria(),
            "detalleCategoria" => $categoria->getDetalleCategoria()
        ]);
    }

    public function eliminar(int $id): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE categorias
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $id
        ]);

    }


    private function convertirEnCategoria (array $fila): Categoria
    {
        return new Categoria(
            $fila["id"],
            $fila["nombreCategoria"],
            $fila["detalleCategoria"]
        );
    }

    public function existeNombreCategoria(string $nombre): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id
                FROM categorias
                WHERE nombreCategoria = :nombreCategoria
                AND deleted_at IS NULL";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "nombreCategoria" => $nombre
        ]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }

    public function existeNombreCategoriaPorId(string $nombre, int $id): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id 
                FROM categorias
                WHERE nombreCategoria = :nombre
                AND id != :id
                AND deleted_at IS NULL";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute(["nombre" => $nombre ,"id" => $id]);

        $fila = $sentencia->fetch();

        return $fila !== false;

    }
}