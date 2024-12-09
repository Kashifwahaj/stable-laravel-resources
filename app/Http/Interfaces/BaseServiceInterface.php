<?php

namespace App\Http\Interfaces;

interface BaseServiceInterface
{
    public function getAll(
        array $filters = [],
        string $search = '',
        string $sortBy = 'id',
        string $sortOrder = 'asc',
        int $perPage = 15
    );

    public function create(array $data);

    public function find(int | string $id);

    public function update(int | string $id, array $data);

    public function delete(int | string $id);

    /**
     * Get the view name for the index page.
     *
     * @return string
     */
    public function getIndexView(): string;

    /**
     * Get the view name for the show page.
     *
     * @return string
     */
    public function getShowView(): string;

    /**
     * Get the view name for the edit page.
     *
     * @return string
     */
    public function getEditView(): string;
}
