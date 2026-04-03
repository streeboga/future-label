<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\PaymentProviderInterface;
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
use App\Services\Payment\StubPaymentProvider;
use App\Services\TelegramNotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentProviderInterface::class, StubPaymentProvider::class);

        $this->app->singleton(TelegramNotificationService::class, fn (): TelegramNotificationService => new TelegramNotificationService(
            botToken: (string) config('services.telegram.bot_token'),
            chatId: (string) config('services.telegram.chat_id'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Hash::setRounds(12);

        // Event-Listener mappings for notifications
        Event::listen(ReleaseStatusChanged::class, SendReleaseStatusChangedNotification::class);
        Event::listen(PaymentConfirmed::class, SendPaymentConfirmedNotification::class);
        Event::listen(ContractGenerated::class, SendContractGeneratedNotification::class);
        Event::listen(ReleaseSubmittedForReview::class, SendReleaseSubmittedTelegramNotification::class);
        Event::listen(UserRegistered::class, SendNewArtistTelegramNotification::class);
    }
}
