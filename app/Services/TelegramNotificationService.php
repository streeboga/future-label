<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final readonly class TelegramNotificationService
{
    public function __construct(
        private string $botToken,
        private string $chatId,
    ) {}

    public function send(string $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram notification skipped: bot token or chat ID not configured.');

            return false;
        }

        try {
            $response = Http::post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ],
            );

            if ($response->failed()) {
                Log::error('Telegram notification failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram notification error.', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
