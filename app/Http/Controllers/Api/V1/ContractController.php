<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ContractStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Release;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ContractController extends Controller
{
    public function __construct(
        private readonly ContractService $service,
    ) {}

    /**
     * List contracts
     *
     * Returns paginated list of contracts. Artists see only their own contracts.
     * Admins and managers see all contracts.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        Gate::authorize('viewAny', Contract::class);

        $filters = [];

        $statusFilter = $request->query('filter');
        if (is_array($statusFilter) && isset($statusFilter['status']) && is_string($statusFilter['status'])) {
            $status = ContractStatus::tryFrom($statusFilter['status']);
            if ($status !== null) {
                $filters['status'] = $status;
            }
        }

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        if ($user->role === UserRole::Admin || $user->role === UserRole::Manager) {
            $contracts = $this->service->listAll($filters, $perPage);
        } else {
            $contracts = $this->service->listForUser($user, $filters, $perPage);
        }

        return ContractResource::collection($contracts);
    }

    /**
     * Generate a contract for a release
     *
     * Creates a new contract PDF for the given release.
     */
    public function store(StoreContractRequest $request, Release $release): JsonResponse
    {
        Gate::authorize('create', Contract::class);

        /** @var User $user */
        $user = $request->user();

        $contract = $this->service->generate($user, $release, $request->toDto());

        return ContractResource::make($contract)
            ->response()
            ->setStatusCode(201)
            ->header('Location', "/api/v1/contracts/{$contract->key}");
    }

    /**
     * Accept a contract
     *
     * Sets the contract status to accepted, recording IP and user agent.
     */
    public function accept(Request $request, Contract $contract): ContractResource
    {
        Gate::authorize('accept', $contract);

        $ip = $request->ip() ?? '0.0.0.0';
        $userAgent = $request->userAgent() ?? 'unknown';

        $updatedContract = $this->service->accept($contract, $ip, $userAgent);

        return ContractResource::make($updatedContract);
    }

    /**
     * Download contract PDF
     *
     * Returns the PDF file for download.
     */
    public function downloadPdf(Contract $contract): StreamedResponse
    {
        Gate::authorize('download', $contract);

        $path = $this->service->getPdfPath($contract);

        return Storage::disk('local')->download($path, "{$contract->key}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
