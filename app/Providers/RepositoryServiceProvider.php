<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ReleaseRepositoryInterface;
use App\Repositories\Contracts\ServiceCatalogRepositoryInterface;
use App\Repositories\Contracts\TrackRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ReleaseRepository;
use App\Repositories\Eloquent\ServiceCatalogRepository;
use App\Repositories\Eloquent\TrackRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ServiceCatalogRepositoryInterface::class => ServiceCatalogRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        ReleaseRepositoryInterface::class => ReleaseRepository::class,
        TrackRepositoryInterface::class => TrackRepository::class,
    ];
}
