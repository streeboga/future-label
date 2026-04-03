<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Release;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $service,
    ) {}

    /**
     * List payments
     *
     * Returns paginated list of payments for the current user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        Gate::authorize('viewAny', Payment::class);

        $perPage = min((int) ($request->query('per_page', '15')), 100);

        $payments = $this->service->listForUser($user, [], $perPage);

        return PaymentResource::collection($payments);
    }

    /**
     * Show payment
     *
     * Returns a single payment by public key.
     */
    public function show(Payment $payment): PaymentResource
    {
        Gate::authorize('view', $payment);

        return PaymentResource::make($payment);
    }

    /**
     * Initiate payment for release
     *
     * Creates a payment for the given release. Method must be 'online' or 'manual'.
     */
    public function store(StorePaymentRequest $request, Release $release): JsonResponse
    {
        Gate::authorize('create', Payment::class);

        /** @var User $user */
        $user = $request->user();

        $payment = $this->service->initiatePayment($user, $release, $request->toDto());

        return PaymentResource::make($payment)
            ->response()
            ->setStatusCode(201)
            ->header('Location', "/api/v1/payments/{$payment->key}");
    }
}
