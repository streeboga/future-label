<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $service,
    ) {}

    /**
     * List notifications
     *
     * Returns paginated list of notifications for the authenticated user, newest first.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        Gate::authorize('viewAny', Notification::class);

        $filters = [];

        $typeFilter = $request->query('filter');
        if (is_array($typeFilter) && isset($typeFilter['type']) && is_string($typeFilter['type'])) {
            $type = NotificationType::tryFrom($typeFilter['type']);
            if ($type !== null) {
                $filters['type'] = $type;
            }
        }

        if (is_array($typeFilter) && isset($typeFilter['unread']) && $typeFilter['unread'] === 'true') {
            $filters['unread'] = true;
        }

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        $notifications = $this->service->listForUser($user, $filters, $perPage);

        return NotificationResource::collection($notifications);
    }

    /**
     * Mark notification as read
     *
     * Marks a single notification as read by setting read_at timestamp.
     */
    public function markAsRead(Notification $notification): NotificationResource
    {
        Gate::authorize('markAsRead', $notification);

        $updated = $this->service->markAsRead($notification);

        return NotificationResource::make($updated);
    }
}
