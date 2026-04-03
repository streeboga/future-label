<?php

declare(strict_types=1);

use App\Enums\NotificationType;
use App\Enums\ReleaseStatus;
use App\Events\ContractGenerated;
use App\Events\PaymentConfirmed;
use App\Events\ReleaseStatusChanged;
use App\Events\ReleaseSubmittedForReview;
use App\Events\UserRegistered;
use App\Listeners\SendContractGeneratedNotification;
use App\Listeners\SendNewArtistTelegramNotification;
use App\Listeners\SendPaymentConfirmedNotification;
use App\Listeners\SendReleaseStatusChangedNotification;
use App\Listeners\SendReleaseSubmittedTelegramNotification;
use App\Models\Order;
use App\Models\Release;
use App\Models\User;
use App\Notifications\ContractGeneratedNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\ReleaseStatusChangedNotification;
use App\Services\TelegramNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

covers(
    SendReleaseStatusChangedNotification::class,
    SendPaymentConfirmedNotification::class,
    SendContractGeneratedNotification::class,
    SendReleaseSubmittedTelegramNotification::class,
    SendNewArtistTelegramNotification::class,
);

// --- ReleaseStatusChanged Listener ---

it('creates in-app notification and sends email when release is approved', function (): void {
    Notification::fake();

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->inReview()->create();

    $event = new ReleaseStatusChanged($release, ReleaseStatus::InReview, ReleaseStatus::Approved);
    $listener = app(SendReleaseStatusChangedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'type' => NotificationType::ReleaseStatusChanged->value,
    ]);

    Notification::assertSentTo($user, ReleaseStatusChangedNotification::class);
});

it('creates in-app notification when release is rejected', function (): void {
    Notification::fake();

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->rejected()->create();

    $event = new ReleaseStatusChanged($release, ReleaseStatus::InReview, ReleaseStatus::Rejected);
    $listener = app(SendReleaseStatusChangedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'type' => NotificationType::ReleaseStatusChanged->value,
    ]);
});

it('creates in-app notification when release is published', function (): void {
    Notification::fake();

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->published()->create();

    $event = new ReleaseStatusChanged($release, ReleaseStatus::Approved, ReleaseStatus::Published);
    $listener = app(SendReleaseStatusChangedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'type' => NotificationType::ReleaseStatusChanged->value,
    ]);
});

it('does not create notification for non-notifiable status changes', function (): void {
    Notification::fake();

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->draft()->create();

    $event = new ReleaseStatusChanged($release, ReleaseStatus::Draft, ReleaseStatus::InReview);
    $listener = app(SendReleaseStatusChangedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseCount('notifications', 0);
    Notification::assertNothingSent();
});

// --- PaymentConfirmed Listener ---

it('creates in-app notification and sends telegram when payment is confirmed', function (): void {
    Notification::fake();
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
    config(['services.telegram.bot_token' => 'test-token', 'services.telegram.chat_id' => 'test-chat']);
    $this->app->forgetInstance(TelegramNotificationService::class);

    $user = User::factory()->artist()->create();
    $order = Order::factory()->for($user)->create();

    $event = new PaymentConfirmed($order);
    $listener = app(SendPaymentConfirmedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'type' => NotificationType::PaymentConfirmed->value,
    ]);

    Notification::assertSentTo($user, PaymentConfirmedNotification::class);
});

// --- ContractGenerated Listener ---

it('creates in-app notification and sends email when contract is generated', function (): void {
    Notification::fake();

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->create();
    $contractUrl = 'https://example.com/contracts/test.pdf';

    $event = new ContractGenerated($user, $release, $contractUrl);
    $listener = app(SendContractGeneratedNotification::class);
    $listener->handle($event);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'type' => NotificationType::ContractGenerated->value,
    ]);

    Notification::assertSentTo($user, ContractGeneratedNotification::class);
});

// --- ReleaseSubmittedForReview Telegram Listener ---

it('sends telegram notification when release is submitted for review', function (): void {
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
    config(['services.telegram.bot_token' => 'test-token', 'services.telegram.chat_id' => 'test-chat']);
    $this->app->forgetInstance(TelegramNotificationService::class);

    $user = User::factory()->artist()->create();
    $release = Release::factory()->for($user)->inReview()->create();

    $event = new ReleaseSubmittedForReview($release);
    $listener = app(SendReleaseSubmittedTelegramNotification::class);
    $listener->handle($event);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.telegram.org'));
});

// --- UserRegistered Telegram Listener ---

it('sends telegram notification when new artist registers', function (): void {
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
    config(['services.telegram.bot_token' => 'test-token', 'services.telegram.chat_id' => 'test-chat']);
    $this->app->forgetInstance(TelegramNotificationService::class);

    $user = User::factory()->artist()->create();

    $event = new UserRegistered($user);
    $listener = app(SendNewArtistTelegramNotification::class);
    $listener->handle($event);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.telegram.org'));
});
