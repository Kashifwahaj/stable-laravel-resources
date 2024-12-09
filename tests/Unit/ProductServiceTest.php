<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Http\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Test for getting all products with filters and pagination.
     */
    public function test_get_all_products_with_filters_and_pagination()
    {
        // Create some products
        Product::factory()->count(5)->create();

        // Mock ProductService
        $productService = app(ProductService::class);

        $filters = [];
        $search = '';
        $sortBy = 'name';
        $sortOrder = 'asc';
        $perPage = 5;

        $products = $productService->getAll($filters, $search, $sortBy, $sortOrder, $perPage);
        // Assert products are paginated
        $this->assertCount(5, $products->items());
    }

    /**
     * Test for creating a product.
     */
    public function test_create_product()
    {
        // Mock data
        $data = [
            'name' => 'Product 1',
            'description' => 'Product 1 Description',
            'price' => 100.00,
        ];

        // Mock ProductService
        $productService = app(ProductService::class);

        // Call create method
        $product = $productService->create($data);

        // Assert the product was created
        $this->assertDatabaseHas('products', [
            'name' => 'Product 1',
            'description' => 'Product 1 Description',
            'price' => 100.00,
        ]);
    }

    /**
     * Test for finding a product by ID.
     */
    public function test_find_product_by_id()
    {
        $product = Product::factory()->create();

        $productService = app(ProductService::class);

        // Call the find method
        $foundProduct = $productService->find($product->id);

        // Assert that the correct product is returned
        $this->assertEquals($product->id, $foundProduct->id);
    }

    /**
     * Test for updating a product.
     */
    public function test_update_product()
    {
        $product = Product::factory()->create();

        // Data to update
        $data = [
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price' => 150.00,
        ];

        $productService = app(ProductService::class);

        // Call the update method
        $updatedProduct = $productService->update($product, $data);

        // Assert that the product was updated
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price' => 150.00,
        ]);
    }

    /**
     * Test for deleting a product.
     */
    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $productService = app(ProductService::class);

        // Call the delete method
        $productService->delete($product);

        // Assert that the product is deleted
        $this->assertModelMissing($product);
    }

    public function test_filter_products_by_price()
    {
        // Create two products with different prices
        Product::factory()->create(['name' => 'Product 1', 'price' => 100]);
        Product::factory()->create(['name' => 'Product 2', 'price' => 200]);

        $productService = app(ProductService::class);

        // Apply filter to get products with price = 100
        $filters = ['price' => 100];
        $products = $productService->getAll($filters);

        $this->assertCount(1, $products->items());
        $this->assertEquals('Product 1', $products->items()[0]['name']);
    }
    public function test_search_products_by_name_or_description()
    {
        // Create two products with different names and descriptions
        Product::factory()->create(['name' => 'Product 1', 'description' => 'First product']);
        Product::factory()->create(['name' => 'Product 2', 'description' => 'Second product']);

        $productService = app(ProductService::class);

        // Search for products with 'first' in the name or description
        $search = 'first';
        $products = $productService->getAll([], $search);

        $this->assertCount(1, $products->items());
        $this->assertEquals('Product 1', $products->items()[0]['name']);
    }

    public function test_sort_products_by_name()
    {
        // Create two products
        Product::factory()->create(['name' => 'Product 1']);
        Product::factory()->create(['name' => 'Product 2']);

        $productService = app(ProductService::class);

        // Sort products by name in ascending order
        $sortBy = 'name';
        $sortOrder = 'asc';
        $products = $productService->getAll([], '', $sortBy, $sortOrder);

        $this->assertEquals('Product 1', $products->items()[0]['name']);
        $this->assertEquals('Product 2', $products->items()[count($products->items()) - 1]['name']);
    }

    public function test_sort_products_in_descending_order_by_name()
    {
        // Create two products
        Product::factory()->create(['name' => 'Product 1']);
        Product::factory()->create(['name' => 'Product 2']);

        $productService = app(ProductService::class);

        // Sort products by name in descending order
        $sortBy = 'name';
        $sortOrder = 'desc';
        $products = $productService->getAll([], '', $sortBy, $sortOrder);
        $this->assertEquals('Product 2', $products->items()[0]['name']);
        $this->assertEquals('Product 1', $products->items()[count($products->items()) - 1]['name']);
    }

    public function test_paginate_products()
    {
        // Create 20 products
        Product::factory()->count(20)->create();

        $productService = app(ProductService::class);

        // Request products per page
        $perPage = 5;
        $products = $productService->getAll([], '', 'id', 'asc', $perPage);

        $this->assertCount(5, $products->items()); // Assert 5 products per page
        $this->assertEquals(20, $products->total()); // Assert total products count is 20
    }
}
