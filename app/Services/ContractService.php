<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Contract\CreateContractData;
use App\Enums\ContractStatus;
use App\Events\ContractGenerated;
use App\Models\Contract;
use App\Models\Release;
use App\Models\User;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final readonly class ContractService
{
    public function __construct(
        private ContractRepositoryInterface $repository,
        private ReleaseService $releaseService,
    ) {}

    public function generate(User $user, Release $release, CreateContractData $data): Contract
    {
        if ($release->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'release' => ['You can only generate contracts for your own releases.'],
            ]);
        }

        // Check if there is already a pending or accepted contract for this release
        $existing = $this->repository->findAcceptedForRelease($release->id);
        if ($existing !== null) {
            throw ValidationException::withMessages([
                'release' => ['An accepted contract already exists for this release.'],
            ]);
        }

        /** @var Contract */
        $contract = DB::transaction(function () use ($user, $release, $data): Contract {
            $contract = $this->repository->create([
                'user_id' => $user->id,
                'release_id' => $release->id,
                'template_version' => $data->template_version,
                'status' => ContractStatus::Pending,
            ]);

            $pdfUrl = $this->generatePdf($contract, $user, $release);

            return $this->repository->update($contract, ['pdf_url' => $pdfUrl]);
        });

        ContractGenerated::dispatch($user, $release, $contract->pdf_url ?? '');

        return $contract;
    }

    public function accept(Contract $contract, string $ip, string $userAgent): Contract
    {
        if ($contract->status !== ContractStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => ['Only pending contracts can be accepted.'],
            ]);
        }

        /** @var Contract */
        $contract = DB::transaction(fn (): Contract => $this->repository->update($contract, [
            'status' => ContractStatus::Accepted,
            'accepted_at' => now(),
            'accepted_ip' => $ip,
            'accepted_user_agent' => $userAgent,
        ]));

        // Transition release: AwaitingContract → InReview
        $this->releaseService->transitionAfterContract($contract->release);

        return $contract;
    }

    public function findByKey(string $key): Contract
    {
        return $this->repository->findByKey($key);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function listForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForUser($user->id, $filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Contract>
     */
    public function listAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findAcceptedForRelease(int $releaseId): ?Contract
    {
        return $this->repository->findAcceptedForRelease($releaseId);
    }

    public function getPdfPath(Contract $contract): string
    {
        if ($contract->pdf_url === null) {
            throw ValidationException::withMessages([
                'pdf' => ['Contract PDF has not been generated yet.'],
            ]);
        }

        $path = $contract->pdf_url;

        if (! Storage::disk('local')->exists($path)) {
            throw ValidationException::withMessages([
                'pdf' => ['Contract PDF file not found.'],
            ]);
        }

        return $path;
    }

    private function generatePdf(Contract $contract, User $user, Release $release): string
    {
        $pdf = Pdf::loadView('contracts.offer', [
            'contract' => $contract,
            'user' => $user,
            'release' => $release,
            'date' => now()->format('d.m.Y'),
        ]);

        $directory = 'contracts';
        Storage::disk('local')->makeDirectory($directory);

        $filename = "{$directory}/{$contract->key}.pdf";
        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }
}
