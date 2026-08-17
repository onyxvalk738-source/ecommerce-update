<?php

namespace App\Services;

use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Exception;

class CategoriaService 
{
    private CategoriaRepository $categoriaRepository; 

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function validarCategoria(Categoria $categoria):void 
    {
        if(trim($categoria->getNombreCategoria()) === "") {
            throw new Exception("El nombre es obligatorio");
        }

        if(trim($categoria->getDetalleCategoria()) ==="") {
            throw new Exception("El detalle de la categoria es obligatorio");
        }
    }

   public function guardar(Categoria $categoria): void
{
    $this->validarCategoria($categoria);

    if ($this->categoriaRepository->existeNombreCategoria(
        $categoria->getNombreCategoria()
    )) {
        throw new Exception("Ya existe una categoria con ese nombre");
    }

    $this->categoriaRepository->guardar($categoria);
}
   public function obtenerPorId(int $id): Categoria
{
    $categoria = $this->categoriaRepository->obtenerPorId($id);

    if($categoria === null) {
        throw new Exception("La categoria no existe.");
    }

    return $categoria;
}
   
   public function actualizar(Categoria $categoria): void
{
    $this->obtenerPorId($categoria->getId());

    $this->validarCategoria($categoria);

    if($this->categoriaRepository->existeNombreCategoriaPorId(
        $categoria->getNombreCategoria(),
        $categoria->getId()
    )) {
        throw new Exception("Ya existe una categoria con este nombre");
    }

    $this->categoriaRepository->actualizar($categoria);
}


  public function eliminar(int $id): void
{
    $this->obtenerPorId($id);

    $this->categoriaRepository->eliminar($id);
}


}