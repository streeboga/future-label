<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\ServiceCatalogQueryBuilder;
use App\Models\ServiceCatalog;
use App\Repositories\Contracts\ServiceCatalogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ServiceCatalogRepository implements ServiceCatalogRepositoryInterface
{
    /**
     * @return Collection<int, ServiceCatalog>
     */
    public function allActive(): Collection
    {
        return ServiceCatalogQueryBuilder::make()
            ->active()
            ->sortBySortOrder()
            ->getQuery()
            ->get();
    }

    public function findByKey(string $key): ServiceCatalog
    {
        $model = ServiceCatalogQueryBuilder::make()
            ->byKey($key)
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Service not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ServiceCatalog
    {
        return ServiceCatalog::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ServiceCatalog $service, array $data): ServiceCatalog
    {
        $service->fill($data);
        $service->save();

        return $service->refresh();
    }

    public function deactivate(ServiceCatalog $service): ServiceCatalog
    {
        $service->fill(['is_active' => false]);
        $service->save();

        return $service->refresh();
    }
}
