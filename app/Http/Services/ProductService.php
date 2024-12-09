<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Http\Interfaces\BaseServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService implements BaseServiceInterface
{
    protected string $baseViewFolderName;

    public function __construct(protected Product $model)
    {
        $modelName = class_basename($this->model);
        $this->baseViewFolderName = Str::plural(strtolower($modelName));
    }

    /**
     * Retrieve all records with optional filters, search, and sorting.
     */
    public function getAll(
        array $filters = [],
        string $search = '',
        string $sortBy = 'id',
        string $sortOrder = 'asc',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->query();
        // Apply filters
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $query->where($key, $value);
            }
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Return paginated results
        return $query->paginate($perPage);
    }

    /**
     * Create a new record.
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $product = $this->model->create($data);
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Find a record by its ID.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Update an existing record.
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();

        try {
            $model->update($data);
            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a record.
     */
    public function delete($model)
    {
        DB::beginTransaction();

        try {
            $model->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get the index view path.
     */
    public function getIndexView(): string
    {
        return $this->baseViewFolderName . '.index';
    }

    /**
     * Get the show view path.
     */
    public function getShowView(): string
    {
        return $this->baseViewFolderName . '.show';
    }

    /**
     * Get the edit view path.
     */
    public function getEditView(): string
    {
        return $this->baseViewFolderName . '.edit';
    }
}
