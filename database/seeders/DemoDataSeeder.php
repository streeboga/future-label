<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContractStatus;
use App\Enums\NotificationType;
use App\Enums\OrderStatus;
use App\Enums\ServiceCategory;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Release;
use App\Models\ServiceCatalog;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- Fetch existing users from DatabaseSeeder ---
        $admin = User::where('email', 'admin@future-label.com')->firstOrFail();
        $manager = User::where('email', 'manager@future-label.com')->firstOrFail();
        $danp = User::where('email', 'danp@example.com')->firstOrFail();
        $marysol = User::where('email', 'marysol@example.com')->firstOrFail();
        $blagov = User::where('email', 'blagov@example.com')->firstOrFail();
        $otherArtists = User::whereNotIn('email', [
            'admin@future-label.com', 'manager@future-label.com',
            'danp@example.com', 'marysol@example.com', 'blagov@example.com',
        ])->whereNotNull('email_verified_at')->get();

        // =============================================
        // 1. SERVICE CATALOG (8 services)
        // =============================================
        $svcMastering = ServiceCatalog::factory()->create([
            'title' => 'Мастеринг трека',
            'description' => 'Профессиональный мастеринг одного трека для стриминговых площадок.',
            'price' => 3000.00,
            'category' => ServiceCategory::Mastering,
            'sort_order' => 1,
        ]);
        $svcMixing = ServiceCatalog::factory()->create([
            'title' => 'Сведение трека',
            'description' => 'Сведение и балансировка инструментов, вокала и эффектов.',
            'price' => 5000.00,
            'category' => ServiceCategory::Mixing,
            'sort_order' => 2,
        ]);
        $svcDistribution = ServiceCatalog::factory()->create([
            'title' => 'Дистрибуция на площадки',
            'description' => 'Размещение на Spotify, Apple Music, Яндекс.Музыка, VK Музыка и 50+ площадок.',
            'price' => 1500.00,
            'category' => ServiceCategory::Distribution,
            'sort_order' => 3,
        ]);
        $svcPromo = ServiceCatalog::factory()->create([
            'title' => 'Питчинг в плейлисты',
            'description' => 'Отправка релиза кураторам плейлистов Spotify и Apple Music.',
            'price' => 7000.00,
            'category' => ServiceCategory::Promotion,
            'sort_order' => 4,
        ]);
        $svcDesign = ServiceCatalog::factory()->create([
            'title' => 'Дизайн обложки',
            'description' => 'Разработка уникальной обложки для релиза (3000x3000 px).',
            'price' => 4000.00,
            'category' => ServiceCategory::Design,
            'sort_order' => 5,
        ]);
        $svcVideo = ServiceCatalog::factory()->create([
            'title' => 'Lyric-видео',
            'description' => 'Видеоряд с анимированным текстом песни для YouTube.',
            'price' => 12000.00,
            'category' => ServiceCategory::Video,
            'sort_order' => 6,
        ]);
        $svcProduction = ServiceCatalog::factory()->create([
            'title' => 'Аранжировка',
            'description' => 'Создание аранжировки под ваш вокал или мелодию.',
            'price' => 15000.00,
            'category' => ServiceCategory::Production,
            'sort_order' => 7,
        ]);
        ServiceCatalog::factory()->inactive()->create([
            'title' => 'Запись в студии (приостановлено)',
            'description' => 'Запись вокала и инструментов в профессиональной студии.',
            'price' => 25000.00,
            'category' => ServiceCategory::Production,
            'sort_order' => 99,
        ]);

        // =============================================
        // 2. DanP — полный путь: Draft, Published, Rejected
        // =============================================

        // Published single (happy path complete)
        $danpPublished = Release::factory()->published()->single()->create([
            'user_id' => $danp->id,
            'title' => 'Свет в окне',
            'artist_name' => 'DanP',
            'genre' => 'worship',
            'language' => 'ru',
            'description' => 'Первый сингл — история о надежде и вере.',
            'release_date' => now()->subDays(30),
        ]);
        Track::factory()->mp3()->create([
            'release_id' => $danpPublished->id,
            'title' => 'Свет в окне',
            'track_number' => 1,
            'duration_seconds' => 245,
            'file_size' => 8_500_000,
            'authors' => 'Даниил Петров',
            'composers' => 'Даниил Петров',
        ]);
        Contract::factory()->accepted()->withPdf()->create([
            'user_id' => $danp->id,
            'release_id' => $danpPublished->id,
        ]);
        Payment::factory()->confirmed()->online()->create([
            'user_id' => $danp->id,
            'release_id' => $danpPublished->id,
            'amount' => 4500.00,
            'confirmed_by' => $manager->id,
        ]);
        $danpPublished->services()->attach([$svcDistribution->id, $svcMastering->id]);

        // Draft EP (work in progress)
        $danpDraft = Release::factory()->draft()->ep()->create([
            'user_id' => $danp->id,
            'title' => 'Новое утро EP',
            'artist_name' => 'DanP',
            'genre' => 'pop',
            'language' => 'ru',
            'description' => null,
            'release_date' => now()->addMonths(2),
        ]);
        Track::factory()->wav()->create(['release_id' => $danpDraft->id, 'title' => 'Новое утро', 'track_number' => 1, 'duration_seconds' => 210]);
        Track::factory()->wav()->create(['release_id' => $danpDraft->id, 'title' => 'Рассвет', 'track_number' => 2, 'duration_seconds' => 195]);
        Track::factory()->wav()->create(['release_id' => $danpDraft->id, 'title' => 'Дорога домой', 'track_number' => 3, 'duration_seconds' => 260]);

        // Rejected single
        $danpRejected = Release::factory()->rejected()->single()->create([
            'user_id' => $danp->id,
            'title' => 'Тёмная ночь',
            'artist_name' => 'DanP',
            'genre' => 'rock',
            'language' => 'ru',
            'reject_reason' => 'Качество записи не соответствует стандартам. Рекомендуем перезаписать вокал.',
        ]);
        Track::factory()->mp3()->create(['release_id' => $danpRejected->id, 'title' => 'Тёмная ночь', 'track_number' => 1, 'duration_seconds' => 190, 'file_size' => 6_200_000]);

        // =============================================
        // 3. MarySol — InReview, AwaitingPayment
        // =============================================

        // InReview album
        $maryInReview = Release::factory()->inReview()->album()->create([
            'user_id' => $marysol->id,
            'title' => 'Голос сердца',
            'artist_name' => 'MarySol',
            'genre' => 'worship',
            'language' => 'ru',
            'description' => 'Дебютный альбом — 8 треков о любви и вере.',
            'release_date' => now()->addMonths(1),
        ]);
        $albumTracks = ['Вступление', 'Голос сердца', 'Тишина', 'Молитва', 'Свобода', 'Радость', 'Благодарность', 'Финал'];
        foreach ($albumTracks as $i => $trackTitle) {
            Track::factory()->flac()->create([
                'release_id' => $maryInReview->id,
                'title' => $trackTitle,
                'track_number' => $i + 1,
                'duration_seconds' => fake()->numberBetween(180, 320),
                'authors' => 'Мария Соколова',
                'composers' => 'Мария Соколова',
            ]);
        }
        Contract::factory()->accepted()->withPdf()->create([
            'user_id' => $marysol->id,
            'release_id' => $maryInReview->id,
        ]);
        Payment::factory()->paid()->online()->create([
            'user_id' => $marysol->id,
            'release_id' => $maryInReview->id,
            'amount' => 12000.00,
        ]);
        $maryInReview->services()->attach([$svcDistribution->id, $svcMastering->id, $svcPromo->id]);

        // AwaitingPayment single (just submitted, needs to pay)
        $maryAwaiting = Release::factory()->awaitingPayment()->single()->create([
            'user_id' => $marysol->id,
            'title' => 'Утренняя звезда',
            'artist_name' => 'MarySol',
            'genre' => 'pop',
            'language' => 'ru',
            'release_date' => now()->addMonths(3),
        ]);
        Track::factory()->wav()->create([
            'release_id' => $maryAwaiting->id,
            'title' => 'Утренняя звезда',
            'track_number' => 1,
            'duration_seconds' => 230,
        ]);

        // =============================================
        // 4. BLAGOV — Approved (waiting publish), Published
        // =============================================

        // Approved single
        $blagovApproved = Release::factory()->approved()->single()->create([
            'user_id' => $blagov->id,
            'title' => 'Огонь',
            'artist_name' => 'BLAGOV',
            'genre' => 'electronic',
            'language' => 'ru',
            'description' => 'Электронный сингл с элементами worship.',
            'release_date' => now()->addWeeks(2),
        ]);
        Track::factory()->wav()->create(['release_id' => $blagovApproved->id, 'title' => 'Огонь', 'track_number' => 1, 'duration_seconds' => 275, 'file_size' => 45_000_000]);
        Contract::factory()->accepted()->withPdf()->create(['user_id' => $blagov->id, 'release_id' => $blagovApproved->id]);
        Payment::factory()->confirmed()->manual()->create(['user_id' => $blagov->id, 'release_id' => $blagovApproved->id, 'amount' => 1500.00, 'confirmed_by' => $manager->id]);
        $blagovApproved->services()->attach([$svcDistribution->id]);

        // Published EP
        $blagovPublished = Release::factory()->published()->ep()->create([
            'user_id' => $blagov->id,
            'title' => 'Грани',
            'artist_name' => 'BLAGOV',
            'genre' => 'electronic',
            'language' => 'ru',
            'release_date' => now()->subMonths(2),
        ]);
        foreach (['Грани', 'Отражение', 'Горизонт', 'Эхо'] as $i => $t) {
            Track::factory()->flac()->create(['release_id' => $blagovPublished->id, 'title' => $t, 'track_number' => $i + 1, 'duration_seconds' => fake()->numberBetween(200, 300)]);
        }
        Contract::factory()->accepted()->withPdf()->create(['user_id' => $blagov->id, 'release_id' => $blagovPublished->id]);
        Payment::factory()->confirmed()->online()->create(['user_id' => $blagov->id, 'release_id' => $blagovPublished->id, 'amount' => 6500.00, 'confirmed_by' => $admin->id]);
        $blagovPublished->services()->attach([$svcDistribution->id, $svcMastering->id, $svcDesign->id]);

        // AwaitingContract single
        $blagovAwaitingContract = Release::factory()->awaitingContract()->single()->create([
            'user_id' => $blagov->id,
            'title' => 'Путь',
            'artist_name' => 'BLAGOV',
            'genre' => 'hip-hop',
            'language' => 'ru',
        ]);
        Track::factory()->mp3()->create(['release_id' => $blagovAwaitingContract->id, 'title' => 'Путь', 'track_number' => 1, 'duration_seconds' => 215]);
        Payment::factory()->paid()->online()->create(['user_id' => $blagov->id, 'release_id' => $blagovAwaitingContract->id, 'amount' => 1500.00]);
        Contract::factory()->pending()->create(['user_id' => $blagov->id, 'release_id' => $blagovAwaitingContract->id]);

        // =============================================
        // 5. Other artists — drafts and random releases
        // =============================================
        foreach ($otherArtists->take(3) as $artist) {
            $draft = Release::factory()->draft()->single()->create([
                'user_id' => $artist->id,
                'artist_name' => $artist->name,
            ]);
            Track::factory()->mp3()->create(['release_id' => $draft->id, 'title' => fake()->sentence(2), 'track_number' => 1]);
        }

        // =============================================
        // 6. ORDERS (service purchases)
        // =============================================
        Order::factory()->forUser($danp)->forService($svcPromo)->withStatus(OrderStatus::Completed)->create(['release_id' => $danpPublished->id, 'notes' => 'Питчинг для сингла "Свет в окне"']);
        Order::factory()->forUser($danp)->forService($svcDesign)->withStatus(OrderStatus::Paid)->create(['release_id' => $danpDraft->id, 'notes' => 'Обложка для EP "Новое утро"']);
        Order::factory()->forUser($marysol)->forService($svcVideo)->withStatus(OrderStatus::InProgress)->create(['release_id' => $maryInReview->id, 'notes' => 'Lyric-видео для "Голос сердца"']);
        Order::factory()->forUser($marysol)->forService($svcPromo)->withStatus(OrderStatus::Pending)->create(['notes' => 'Питчинг для следующего сингла']);
        Order::factory()->forUser($blagov)->forService($svcMixing)->withStatus(OrderStatus::Completed)->create(['release_id' => $blagovPublished->id]);
        Order::factory()->forUser($blagov)->forService($svcProduction)->withStatus(OrderStatus::Cancelled)->create(['notes' => 'Отменён по запросу артиста']);

        // =============================================
        // 7. NOTIFICATIONS (mixed read/unread)
        // =============================================
        // DanP notifications
        Notification::factory()->releaseStatusChanged()->read()->create(['user_id' => $danp->id, 'title' => 'Релиз "Свет в окне" одобрен', 'body' => 'Ваш релиз прошёл модерацию и одобрен к публикации.', 'data' => ['release_key' => $danpPublished->key]]);
        Notification::factory()->releaseStatusChanged()->read()->create(['user_id' => $danp->id, 'title' => 'Релиз "Свет в окне" опубликован', 'body' => 'Ваш релиз теперь доступен на всех площадках.', 'data' => ['release_key' => $danpPublished->key]]);
        Notification::factory()->paymentConfirmed()->read()->create(['user_id' => $danp->id, 'title' => 'Оплата подтверждена', 'body' => 'Платёж 4 500 ₽ за релиз "Свет в окне" подтверждён.', 'data' => ['release_key' => $danpPublished->key]]);
        Notification::factory()->releaseStatusChanged()->unread()->create(['user_id' => $danp->id, 'title' => 'Релиз "Тёмная ночь" отклонён', 'body' => 'Качество записи не соответствует стандартам. Рекомендуем перезаписать вокал.', 'data' => ['release_key' => $danpRejected->key]]);
        Notification::factory()->contractGenerated()->read()->create(['user_id' => $danp->id, 'title' => 'Договор готов', 'body' => 'Договор для релиза "Свет в окне" сформирован и ожидает подписания.', 'data' => ['release_key' => $danpPublished->key]]);

        // MarySol notifications
        Notification::factory()->releaseStatusChanged()->unread()->create(['user_id' => $marysol->id, 'title' => 'Релиз "Голос сердца" на проверке', 'body' => 'Ваш альбом отправлен на модерацию.', 'data' => ['release_key' => $maryInReview->key]]);
        Notification::factory()->contractGenerated()->unread()->create(['user_id' => $marysol->id, 'title' => 'Договор для "Голос сердца" готов', 'body' => 'Договор сформирован и подписан.', 'data' => ['release_key' => $maryInReview->key]]);
        Notification::factory()->paymentConfirmed()->read()->create(['user_id' => $marysol->id, 'title' => 'Оплата получена', 'body' => 'Платёж 12 000 ₽ за альбом "Голос сердца" получен.', 'data' => ['release_key' => $maryInReview->key]]);

        // BLAGOV notifications
        Notification::factory()->releaseStatusChanged()->read()->create(['user_id' => $blagov->id, 'title' => 'Релиз "Грани" опубликован', 'body' => 'EP "Грани" доступен на площадках.', 'data' => ['release_key' => $blagovPublished->key]]);
        Notification::factory()->releaseStatusChanged()->unread()->create(['user_id' => $blagov->id, 'title' => 'Релиз "Огонь" одобрен', 'body' => 'Сингл одобрен модератором и ожидает публикации.', 'data' => ['release_key' => $blagovApproved->key]]);
        Notification::factory()->contractGenerated()->unread()->create(['user_id' => $blagov->id, 'title' => 'Договор для "Путь" готов', 'body' => 'Подпишите договор для продолжения.', 'data' => ['release_key' => $blagovAwaitingContract->key]]);
        Notification::factory()->paymentConfirmed()->read()->create(['user_id' => $blagov->id, 'title' => 'Оплата подтверждена', 'body' => 'Перевод 1 500 ₽ за "Огонь" подтверждён менеджером.', 'data' => ['release_key' => $blagovApproved->key]]);
    }
}
