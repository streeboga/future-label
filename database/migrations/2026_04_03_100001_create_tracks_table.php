<?php

declare(strict_types=1);

use App\Enums\TrackFormat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 40)->unique();
            $table->foreignId('release_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('track_number')->default(1);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('file_url')->nullable();
            $table->string('format')->default(TrackFormat::Wav->value);
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('authors')->nullable();
            $table->string('composers')->nullable();
            $table->text('lyrics')->nullable();
            $table->string('isrc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
