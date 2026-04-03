<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_services', function (Blueprint $table): void {
            $table->foreignId('release_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['release_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_services');
    }
};
