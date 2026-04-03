<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        User::factory()->admin()->create([
            'name' => 'K. Mazurov',
            'stage_name' => null,
            'email' => 'admin@future-label.com',
            'phone' => '+79001234567',
            'telegram' => '@kmazurov',
        ]);

        // Manager
        User::factory()->manager()->create([
            'name' => 'Алексей Иванов',
            'stage_name' => null,
            'email' => 'manager@future-label.com',
            'phone' => '+79007654321',
            'telegram' => '@alexey_fl',
        ]);

        // Artists with full profiles
        User::factory()->artist()->create([
            'name' => 'Даниил Петров',
            'stage_name' => 'DanP',
            'email' => 'danp@example.com',
            'phone' => '+79001112233',
            'telegram' => '@danp_music',
            'passport_data' => json_encode([
                'series' => '4515',
                'number' => '123456',
                'issued_by' => 'ОВД Краснодар',
                'issue_date' => '2020-05-15',
            ], JSON_THROW_ON_ERROR),
            'bank_details' => json_encode([
                'bik' => '044525225',
                'account' => '40817810099910004312',
                'bank_name' => 'Сбербанк',
            ], JSON_THROW_ON_ERROR),
        ]);

        User::factory()->artist()->create([
            'name' => 'Мария Соколова',
            'stage_name' => 'MarySol',
            'email' => 'marysol@example.com',
            'phone' => '+79003334455',
            'telegram' => '@marysol_worship',
        ]);

        User::factory()->artist()->create([
            'name' => 'Иван Благов',
            'stage_name' => 'BLAGOV',
            'email' => 'blagov@example.com',
            'phone' => '+79005556677',
            'telegram' => '@blagov_music',
            'passport_data' => json_encode([
                'series' => '4510',
                'number' => '654321',
                'issued_by' => 'УФМС Москва',
                'issue_date' => '2019-11-20',
            ], JSON_THROW_ON_ERROR),
            'bank_details' => json_encode([
                'bik' => '044525974',
                'account' => '40817810500000012345',
                'bank_name' => 'Тинькофф',
            ], JSON_THROW_ON_ERROR),
        ]);

        // 5 more artists without full profiles (fresh registrations)
        User::factory()->artist()->count(5)->create();

        // 2 unverified artists
        User::factory()->artist()->unverified()->count(2)->create();

        // Demo data: releases, tracks, contracts, payments, services, orders, notifications
        $this->call(DemoDataSeeder::class);
    }
}
