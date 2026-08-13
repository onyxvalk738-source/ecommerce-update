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
            throw new Exception("")
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
}