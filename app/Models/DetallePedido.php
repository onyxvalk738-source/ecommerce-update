<?php

namespace App\Models;

class DetallePedido{
    private ?int $id;
    private ?int $idPedido;
    private ?int $idProducto;
    private int $cantidad;
    private float $precioUnitario;
    private float $subtotal;

    public function __construct(?int $id, ?int $idPedido, ?int $idProducto, int $cantidad, float $precioUnitario, float $subtotal)
    {
        $this->id = $id;
        $this->idPedido = $idPedido;
        $this->idProducto = $idProducto;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
        $this->subtotal = $subtotal;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPedido(): ?int
    {
        return $this->idPedido;
    }

    public function setIdPedido(?int $idPedido): void
    {
        $this->idPedido = $idPedido;
    }

    public function getIdProducto(): ?int
    {
        return $this->idProducto;
    }

    public function setIdProducto(?int $idProducto): void
    {
        $this->idProducto = $idProducto;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getPrecioUnitario(): float
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(float $precioUnitario): void
    {
        $this->precioUnitario = $precioUnitario;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }
}