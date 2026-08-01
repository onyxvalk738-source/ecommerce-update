<?php

namespace App\Models;

use DateTime;

class Producto{
    private ?int $id;
    private ?int $idCategoria;
    private string $nombre;
    private DateTime $fechaVencimiento;
    private string $informacion;
    private string $codigo;
    private float $precio;
    private int $unidades;
    private bool $estado;

    public function __construct(?int $id, ?int $idCategoria, string $nombre, DateTime $fechaVencimiento, string $informacion, string $codigo, float $precio, int $unidades, bool $estado)
    {
        $this->id = $id;
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->fechaVencimiento = $fechaVencimiento;
        $this->informacion = $informacion;
        $this->codigo = $codigo;
        $this->precio = $precio;
        $this->unidades = $unidades;
        $this->estado = $estado;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCategoria(): ?int
    {
        return $this->idCategoria;
    }
    public function setIdCategoria(?int $idCategoria): void
    {
        $this->idCategoria = $idCategoria;
    }

    public function getNombre (): string
    {
        return $this->nombre;
    }

    public function setNombre (string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getFechaVencimiento ():DateTime
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento (DateTime $fechaVencimiento): void
    {
        $this->fechaVencimiento = $fechaVencimiento;
    }

    public function getInformacion(): string
    {
        return $this->informacion;
    }

    public function setInformacion (string $informacion): void
    {
        $this->informacion = $informacion;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }


    public function getUnidades(): int
    {
        return $this->unidades;
    }


    public function setUnidades (int $unidades): void
    {
        $this->unidades = $unidades;
    }

    public function getEstado (): bool
    {
        return $this->estado;
    }

    public function setEstado (bool $estado): void
    {
        $this->estado = $estado;
    }
    
}