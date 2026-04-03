<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByKey(string $key): User;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User;
}
