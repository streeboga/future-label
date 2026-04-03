<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Contract>
 */
final class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'ctr_'.Str::ulid(),
            'user_id' => User::factory(),
            'release_id' => Release::factory(),
            'template_version' => '1.0',
            'pdf_url' => null,
            'status' => ContractStatus::Pending,
            'accepted_at' => null,
            'accepted_ip' => null,
            'accepted_user_agent' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ContractStatus::Pending,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ContractStatus::Accepted,
            'accepted_at' => now(),
            'accepted_ip' => '127.0.0.1',
            'accepted_user_agent' => 'TestBrowser/1.0',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ContractStatus::Expired,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ContractStatus::Revoked,
        ]);
    }

    public function withPdf(): static
    {
        return $this->state(fn (array $attributes): array => [
            'pdf_url' => 'contracts/ctr_test.pdf',
        ]);
    }
}
