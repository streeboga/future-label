<?php

declare(strict_types=1);

use App\Enums\ReleaseStatus;
use App\Enums\ReleaseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 40)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('artist_name')->nullable();
            $table->string('type')->default(ReleaseType::Single->value);
            $table->string('genre')->nullable();
            $table->string('language')->nullable();
            $table->text('description')->nullable();
            $table->date('release_date')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('status')->default(ReleaseStatus::Draft->value);
            $table->text('reject_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
