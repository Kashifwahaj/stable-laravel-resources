<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\BaseControllerInterface;
use App\Http\Interfaces\BaseServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

abstract class BaseController extends Controller implements BaseControllerInterface
{
    /**
     * The service class for the resource.
     * 
     * @var BaseServiceInterface
     */
    protected BaseServiceInterface $service;

    /**
     * The resource class for individual items.
     * 
     * @var JsonResource
     */
    protected JsonResource $resourceClass;

    /**
     * The resource collection class for multiple items.
     * 
     * @var ResourceCollection|null
     */
    protected ?ResourceCollection $collectionClass;


    protected ?string $storeRequestClass; // Accepts class name as a string
    protected ?string $updateRequestClass; //
    /**
     * BaseController constructor.
     *
     * @param BaseServiceInterface $service
     * @param JsonResource $resourceClass
     * @param ResourceCollection|null $collectionClass
     * @param ?string | null $storeRequestClass
     * @param ?string | null $updateRequestClass
     */

    public function __construct(
        BaseServiceInterface $service,
        JsonResource $resourceClass,
        ?ResourceCollection $collectionClass = null,
        ?string $storeRequestClass = null,
        ?string $updateRequestClass = null
    ) {

        $this->service = $service;
        $this->resourceClass = $resourceClass;
        $this->collectionClass = $collectionClass;
        $this->storeRequestClass = $storeRequestClass;
        $this->updateRequestClass = $updateRequestClass;
    }

    protected function resolveRequest(string $requestClass)
    {
        if (class_exists($requestClass)) {
            return app($requestClass); // Resolves the FormRequest dynamically
        }

        throw new \InvalidArgumentException("Request class {$requestClass} does not exist.");
    }

    /**
     * Display a paginated list of resources.
     *
     * @return  Response | RedirectResponse | JsonResponse
     */
    public function index(Request $request): Response | RedirectResponse | JsonResponse
    {
        $filters = $request->get('filters', []);
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 15);
        $resources = $this->service->getAll($filters, $search, $sortBy, $sortOrder, $perPage);

        $response = $this->collectionClass
            ? new $this->collectionClass($resources)
            : JsonResource::collection($resources);

        return $this->generateResponse($response, $this->service->getIndexView());
    }

    /**
     * Store a newly created resource.
     *
     * @param FormRequest $request
     * @return  Response | RedirectResponse | JsonResponse
     */
    public function store(FormRequest $request): Response | RedirectResponse | JsonResponse
    {
        $request = $this->resolveRequest($this->storeRequestClass);
        $data = $request->validated();

        $resource = $this->service->create($data);

        return $this->generateResponse(
            new $this->resourceClass($resource),
            $this->service->getIndexView(),
            [],
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return Response | RedirectResponse | JsonResponse
     */
    public function show(mixed $id): Response | RedirectResponse | JsonResponse
    {
        $resource = $this->service->find($id);

        return $this->generateResponse(
            new $this->resourceClass($resource),
            $this->service->getShowView()
        );
    }

    /**
     * Update the specified resource.
     *
     * @param FormRequest $request
     * @param mixed $id
     * @return  Response | RedirectResponse | JsonResponse
     */
    public function update(FormRequest $request, mixed $id): Response | RedirectResponse | JsonResponse
    {
        $request = $this->resolveRequest($this->updateRequestClass);
        $data = $request->validated();

        $resource = $this->service->find($id);
        $this->service->update($resource, $data);

        return $this->generateResponse(
            new $this->resourceClass($resource),
            $this->service->getShowView(),
            [],
            204
        );
    }

    /**
     * Remove the specified resource.
     *
     * @param mixed $id
     * @return  Response | RedirectResponse | JsonResponse
     */
    public function destroy(mixed $id): Response | RedirectResponse | JsonResponse
    {
        $resource = $this->service->find($id);
        $this->service->delete($resource);

        return response()->noContent();
    }

    /**
     * Generate a response for different content types.
     *
     * @param JsonResource|ResourceCollection $resource
     * @param string $view
     * @param array $additionalData
     * @param int $statusCode
     * @return  Response | RedirectResponse | JsonResponse
     */
    protected function generateResponse(
        JsonResource|ResourceCollection $resource,
        string $view,
        array $additionalData = [],
        int $statusCode = 200
    ): Response | RedirectResponse | JsonResponse {
        $returnType = request()->header('X-Return-Type', default: 'json');

        if ($returnType === 'view') {
            return response()->view($view, array_merge($resource->resolve(), $additionalData), $statusCode);
        } elseif ($returnType === 'redirect') {
            return redirect()->route($view)->with($additionalData);
        } elseif ($returnType === 'inertia') {
            // For Inertia.js response, we use Inertia::render to send the data to the frontend.
            return Inertia::render($view, array_merge($resource->resolve(), $additionalData))
                ->setStatusCode($statusCode);
        }

        return $resource->response()->setStatusCode($statusCode);
    }
}
