<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ServiceCatalog;
use Illuminate\Database\Eloquent\Collection;

interface ServiceCatalogRepositoryInterface
{
    /**
     * @return Collection<int, ServiceCatalog>
     */
    public function allActive(): Collection;

    public function findByKey(string $key): ServiceCatalog;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ServiceCatalog;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ServiceCatalog $service, array $data): ServiceCatalog;

    public function deactivate(ServiceCatalog $service): ServiceCatalog;
}
