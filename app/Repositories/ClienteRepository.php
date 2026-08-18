<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Cliente;

class ClienteRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function guardar(Cliente $cliente): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "INSERT INTO clientes
                (
                    nombre,
                    apellido,
                    email,
                    telefono,
                    direccion
                ) 
                VALUES
                (
                    :nombre,
                    :apellido,
                    :email,
                    :telefono,
                    :direccion
                )";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "nombre" => $cliente->getNombre(),
            "apellido" => $cliente->getApellido(),
            "email" => $cliente->getEmail(),
            "telefono" => $cliente->getTelefono(),
            "direccion" => $cliente->getDireccion()
        ]);
    }

    public function obtenerTodos(): array
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT * FROM clientes
                WHERE deleted_at IS NULL";
        
        $sentencia = $pdo->query($sql);

        $filas = $sentencia->fetchAll();

        $clientes = [];

        foreach ($filas as $fila) {
            $cliente = $this->convertirEnCliente($fila);

            $clientes[] = $cliente;
        }

        return $clientes;
    }

    public function obtenerPorId(int $id): ?Cliente
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT *FROM clientes
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

        return $this->convertirEnCliente($fila);

    }

     private function convertirEnCliente (array $fila): Cliente
    {
        return new Cliente(
            $fila["id"],
            $fila["nombre"],
            $fila["apellido"],
            $fila["email"],
            $fila["telefono"],
            $fila["direccion"]
        );
    }


    public function actualizar (Cliente $cliente): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE clientes
                SET 
                nombre = :nombre,
                apellido = :apellido,
                email = :email,
                telefono = :telefono,
                direccion = :direccion
                WHERE id = :id";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $cliente->getId(),
            "nombre" => $cliente->getNombre(),
            "apellido" => $cliente->getApellido(),
            "email" => $cliente->getEmail(),
            "telefono" => $cliente->getTelefono(),
            "direccion" => $cliente->getDireccion()
        ]);
    }

    public function eliminar(int $id): void
    {
        $pdo = $this->database->devolverConexion();

        $sql = "UPDATE pedidos
                SET deleted_at = NOW()
                WHERE id = :id";
        
        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "id" => $id
        ]);
    }

    public function existeEmail(string $email): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id FROM clientes
                WHERE email = :email
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "email" => $email
        ]);

    $fila = $sentencia->fetch();

    return $fila !== false;
    }

    public function existeEmailExceptoId(string $email, int $id): bool
    {
        $pdo = $this->database->devolverConexion();

        $sql = "SELECT id FROM clientes
                WHERE email = :email
                AND id != :id
                AND deleted_at IS NULL";

        $sentencia = $pdo->prepare($sql);

        $sentencia->execute([
            "email" => $email,
            "id" => $id
        ]);

        $fila = $sentencia->fetch();

        return $fila !== false;
    }
    
    

}