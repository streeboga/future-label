<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\ServiceCatalog\CreateServiceCatalogData;
use App\DataTransferObjects\ServiceCatalog\UpdateServiceCatalogData;
use App\Models\ServiceCatalog;
use App\Repositories\Contracts\ServiceCatalogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

final readonly class ServiceCatalogService
{
    public function __construct(
        private ServiceCatalogRepositoryInterface $repository,
    ) {}

    /**
     * @return Collection<int, ServiceCatalog>
     */
    public function listActive(): Collection
    {
        return $this->repository->allActive();
    }

    public function findByKey(string $key): ServiceCatalog
    {
        return $this->repository->findByKey($key);
    }

    public function create(CreateServiceCatalogData $data): ServiceCatalog
    {
        /** @var ServiceCatalog */
        return DB::transaction(fn (): ServiceCatalog => $this->repository->create([
            'title' => $data->title,
            'description' => $data->description,
            'price' => $data->price,
            'currency' => $data->currency,
            'category' => $data->category,
            'sort_order' => $data->sort_order,
            'is_active' => $data->is_active,
        ]));
    }

    public function update(ServiceCatalog $service, UpdateServiceCatalogData $data): ServiceCatalog
    {
        $updateData = collect($data->toArray())
            ->reject(fn (mixed $value): bool => $value instanceof Optional)
            ->toArray();

        /** @var ServiceCatalog */
        return DB::transaction(fn (): ServiceCatalog => $this->repository->update($service, $updateData));
    }

    public function deactivate(ServiceCatalog $service): ServiceCatalog
    {
        /** @var ServiceCatalog */
        return DB::transaction(fn (): ServiceCatalog => $this->repository->deactivate($service));
    }
}
