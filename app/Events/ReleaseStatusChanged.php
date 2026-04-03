<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\ReleaseStatus;
use App\Models\Release;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ReleaseStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Release $release,
        public ReleaseStatus $oldStatus,
        public ReleaseStatus $newStatus,
    ) {}
}
