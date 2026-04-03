<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCatalog\StoreServiceCatalogRequest;
use App\Http\Requests\ServiceCatalog\UpdateServiceCatalogRequest;
use App\Http\Resources\ServiceCatalogResource;
use App\Models\ServiceCatalog;
use App\Services\ServiceCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class ServiceCatalogController extends Controller
{
    public function __construct(
        private readonly ServiceCatalogService $service,
    ) {}

    /**
     * List active services
     *
     * Returns all active services from the catalog, sorted by sort_order.
     * This endpoint is publicly accessible.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', ServiceCatalog::class);

        $services = $this->service->listActive();

        return ServiceCatalogResource::collection($services);
    }

    /**
     * Show a single service
     *
     * Returns the details of a specific service from the catalog.
     */
    public function show(ServiceCatalog $service): ServiceCatalogResource
    {
        Gate::authorize('view', $service);

        return ServiceCatalogResource::make($service);
    }

    /**
     * Create a new service
     *
     * Creates a new service in the catalog. Admin/Manager only.
     */
    public function store(StoreServiceCatalogRequest $request): JsonResponse
    {
        Gate::authorize('create', ServiceCatalog::class);

        $service = $this->service->create($request->toDto());

        return ServiceCatalogResource::make($service)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
            ->header('Location', "/api/v1/services/{$service->key}");
    }

    /**
     * Update an existing service
     *
     * Updates a service in the catalog. Admin/Manager only.
     */
    public function update(UpdateServiceCatalogRequest $request, ServiceCatalog $service): ServiceCatalogResource
    {
        Gate::authorize('update', $service);

        $updated = $this->service->update($service, $request->toDto());

        return ServiceCatalogResource::make($updated);
    }

    /**
     * Deactivate a service (soft delete)
     *
     * Sets is_active=false on the service. Admin/Manager only.
     */
    public function destroy(ServiceCatalog $service): JsonResponse
    {
        Gate::authorize('delete', $service);

        $this->service->deactivate($service);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
