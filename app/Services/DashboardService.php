<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ReleaseStatus;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ReleaseRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

final readonly class DashboardService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ReleaseRepositoryInterface $releaseRepository,
        private PaymentRepositoryInterface $paymentRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getMetrics(): array
    {
        return [
            'total_artists' => $this->userRepository->count(),
            'releases_this_month' => $this->releaseRepository->countCreatedThisMonth(),
            'total_revenue' => $this->paymentRepository->sumConfirmedAmount(),
            'pending_moderation' => $this->releaseRepository->countByStatus(ReleaseStatus::InReview),
        ];
    }
}
