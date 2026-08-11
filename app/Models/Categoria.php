<?php

namespace App\Models;

class Categoria{
    private ?int $id;
    private string $nombreCategoria;
    private string $detalleCategoria;

    public function __construct(?int $id, string $nombreCategoria, string $detalleCategoria)
    {
        $this->id = $id;
        $this->nombreCategoria = $nombreCategoria;
        $this->detalleCategoria = $detalleCategoria;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreCategoria(): string
    {
        return $this->nombreCategoria;
    }

    public function setNombreCategoria(string $nombreCategoria): void
    {
        $this->nombreCategoria = $nombreCategoria;
    }

    public function getDetalleCategoria(): string
    {
        return $this->detalleCategoria;
    }

    public function setDetalleCategoria(string $detalleCategoria): void
    {
        $this->detalleCategoria = $detalleCategoria;
    }
}