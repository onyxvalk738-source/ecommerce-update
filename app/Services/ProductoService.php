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
        $this->validarProducto($producto);

        if($this->repository->existeCodigo($producto->getCodigo())) {
        throw new Exception("Ya existe un producto con este codigo");
    }

        $this->repository->guardar($producto);
    }

    public function obtenerPorId (int $id): Producto
    {
        $producto = $this->repository->obtenerPorId($id);

        if($producto===null){
            throw new Exception("El producto no existe.");
        }

        return $producto;
    }

    public function actualizar(Producto $producto): void
    {
        $this->obtenerPorId($producto->getId());

        $this->validarProducto($producto);

        if($this->repository->existeCodigoExceptoId(
            $producto->getCodigo(),
            $producto->getId()
        )) {
            throw new Exception("Ya existe un producto con este codigo");
        }

        $this->repository->actualizar($producto);
    }

    private function validarProducto(Producto $producto): void
    {
        if($producto->getPrecio() <= 0 ) {
            throw new Exception("El precio debe ser mayor que cero");
        }
        if($producto->getUnidades() < 0){
            throw new Exception("El nombre es obligatorio");
        }
        if(trim($producto->getNombre())=== "") {
            throw new Exception("El codigo es obligatorio");
        }
    }

    public function eliminar(int $id): void
    {
        $this->obtenerPorId($id);

        $this->repository->eliminar($id);
    }
}



