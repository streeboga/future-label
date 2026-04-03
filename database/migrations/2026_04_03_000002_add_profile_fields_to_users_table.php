<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('stage_name', 255)->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('telegram', 100)->nullable()->after('phone');
            $table->text('passport_data')->nullable()->after('telegram');
            $table->text('bank_details')->nullable()->after('passport_data');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['stage_name', 'phone', 'telegram', 'passport_data', 'bank_details']);
        });
    }
};
