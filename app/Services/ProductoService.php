<?php

namespace App\Services;

use App\Models\Producto;
use App\Repositories\ProductoRepository;
use Exception;

class ProductoService
{
    private ProductoRepository $repository;

    public function __construct(ProductoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function guardar (Producto $producto): void
    {
        $this->repository->guardar($producto);

        if($producto->getPrecio() <= 0){
            throw new Exception("El precio del producto debe ser mayor que cero");
        }

        if(trim($producto->getNombre())=== "") {
            throw new Exception("El nombre del producto es obligatorio");
        }

        if($this->repository->existeCodigo($producto->getCodigo())) {
        throw new Exception("Ya existe un producto con este codigo");
    }

        $this->repository->guardar($producto);
    }
}


