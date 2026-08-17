<?php

namespace App\Models;

use DateTime;

class Pedido{

    public const ESTADO_PENDIENTE  = "pendiente";
    public const ESTADO_PROCESADO  = "procesado";
    public const ESTADO_ENVIADO    = "enviado";
    public const ESTADO_ENTREGADO  = "entregado";
    public const ESTADO_CANCELADO  = "cancelado";

    private ?int $id;
    private ?int $idCliente;
    private DateTime $fechaPedido;
    private string $estado;
    private float $total;

    public function __construct(?int $id, ?int $idCliente, DateTime $fechaPedido, string $estado, float $total)
    {
        $this->id = $id;
        $this->idCliente = $idCliente;
        $this->fechaPedido = $fechaPedido;
        $this->estado = $estado;
        $this->total = $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCliente(): ?int
    {
        return $this->idCliente;
    }

    public function setIdCliente(?int $idCliente): void
    {
        $this->idCliente = $idCliente;
    }

    public function getFechaPedido(): DateTime
    {
        return $this->fechaPedido;
    }

    public function setFechaPedido(DateTime $fechaPedido): void
    {
        $this->fechaPedido = $fechaPedido;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }
}