<?php

declare(strict_types=1);

use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

covers(TelegramNotificationService::class);

it('sends message to telegram API successfully', function (): void {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $service = new TelegramNotificationService('test-token', 'test-chat-id');
    $result = $service->send('Test message');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.telegram.org/bottest-token/sendMessage')
            && $request['chat_id'] === 'test-chat-id'
            && $request['text'] === 'Test message'
            && $request['parse_mode'] === 'HTML';
    });
});

it('returns false and logs error when telegram API fails', function (): void {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => false], 400),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'Telegram notification failed'));

    $service = new TelegramNotificationService('test-token', 'test-chat-id');
    $result = $service->send('Test message');

    expect($result)->toBeFalse();
});

it('returns false when bot token is empty', function (): void {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'bot token or chat ID not configured'));

    $service = new TelegramNotificationService('', 'test-chat-id');
    $result = $service->send('Test message');

    expect($result)->toBeFalse();
});

it('returns false when chat id is empty', function (): void {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'bot token or chat ID not configured'));

    $service = new TelegramNotificationService('test-token', '');
    $result = $service->send('Test message');

    expect($result)->toBeFalse();
});

it('catches exceptions and returns false', function (): void {
    Http::fake(function () {
        throw new RuntimeException('Connection failed');
    });

    Log::shouldReceive('error')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'Telegram notification error'));

    $service = new TelegramNotificationService('test-token', 'test-chat-id');
    $result = $service->send('Test message');

    expect($result)->toBeFalse();
});
