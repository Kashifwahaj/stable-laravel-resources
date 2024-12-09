<?php

namespace App\Http\Interfaces;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface BaseControllerInterface
{
    public function index(Request $request): Response | RedirectResponse | JsonResponse;

    public function show(mixed $id): Response | RedirectResponse | JsonResponse;

    public function store(FormRequest $request): Response | RedirectResponse | JsonResponse;

    public function update(FormRequest $request, $id): Response | RedirectResponse | JsonResponse;

    public function destroy($model): Response | RedirectResponse | JsonResponse;
}
