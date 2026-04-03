<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

final class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service,
    ) {}

    /**
     * Admin dashboard metrics
     *
     * Returns real metrics: total artists, releases this month, total revenue, pending moderation count.
     */
    public function __invoke(): JsonResponse
    {
        $metrics = $this->service->getMetrics();

        return response()->json([
            'data' => [
                'type' => 'dashboard',
                'id' => 'admin',
                'attributes' => $metrics,
            ],
        ]);
    }
}
