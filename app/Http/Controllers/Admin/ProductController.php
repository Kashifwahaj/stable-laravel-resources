<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Services\ProductService;

class ProductController extends BaseController
{
    public function __construct(ProductService $productService)
    {
        parent::__construct(
            $productService,
            new ProductResource(resource: []),
            new ProductCollection([]),
            StoreProductRequest::class,
            updateRequestClass: UpdateProductRequest::class
        );
    }




    // Add custom methods if needed
}
