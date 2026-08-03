<?php

namespace App\Services;

use App\Repositories\ProductoRepository;

class ProductoService
{
    private ProductoRepository $repository;

    public function __construct(ProductoRepository $repository)
    {
        $this->repository = $repository;
    }
}


