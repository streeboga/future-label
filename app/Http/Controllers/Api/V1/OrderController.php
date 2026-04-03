<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service,
    ) {}

    /**
     * List orders for the authenticated user
     *
     * Returns all orders belonging to the current user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Order::class);

        /** @var User $user */
        $user = $request->user();

        $orders = $this->service->listForUser($user);

        return OrderResource::collection($orders);
    }

    /**
     * Create a new order
     *
     * Creates a new order for a service. Artist places an order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        Gate::authorize('create', Order::class);

        /** @var User $user */
        $user = $request->user();

        $order = $this->service->create($user, $request->toDto());
        $order->load(['service', 'user']);

        return OrderResource::make($order)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
            ->header('Location', "/api/v1/orders/{$order->key}");
    }
}
