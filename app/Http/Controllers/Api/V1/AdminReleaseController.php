<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReleaseStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use App\Services\ReleaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AdminReleaseController extends Controller
{
    public function __construct(
        private readonly ReleaseService $service,
    ) {}

    /**
     * List all releases (admin)
     *
     * Returns paginated list of all releases with optional filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [];

        $filterParam = $request->query('filter');
        if (is_array($filterParam)) {
            if (isset($filterParam['status']) && is_string($filterParam['status'])) {
                $status = ReleaseStatus::tryFrom($filterParam['status']);
                if ($status !== null) {
                    $filters['status'] = $status;
                }
            }

            if (isset($filterParam['artist']) && is_string($filterParam['artist'])) {
                $filters['user_id'] = (int) $filterParam['artist'];
            }
        }

        $search = $request->query('search');
        if (is_string($search) && $search !== '') {
            $filters['search'] = $search;
        }

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        $releases = $this->service->listAll($filters, $perPage);

        return ReleaseResource::collection($releases);
    }

    /**
     * Show release detail (admin)
     *
     * Returns a single release with tracks, contracts, and user.
     */
    public function show(Release $release): ReleaseResource
    {
        $release->load(['tracks', 'contracts', 'user']);

        return ReleaseResource::make($release);
    }

    /**
     * Change release status (admin)
     *
     * Approve, reject, or publish a release.
     */
    public function updateStatus(Request $request, Release $release): ReleaseResource
    {
        $request->validate([
            'action' => ['required', 'string', 'in:approve,reject,publish'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var string $action */
        $action = $request->input('action');

        $release = match ($action) {
            'approve' => $this->service->approve($release),
            'reject' => $this->service->reject($release, (string) ($request->input('comment', 'Rejected by admin.'))),
            'publish' => $this->service->publish($release),
            default => $release,
        };

        return ReleaseResource::make($release);
    }
}
