import { createFileRoute, Link } from '@tanstack/react-router';
import { useRelease, useReleaseTracks } from '@/hooks/use-releases';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { ReleaseStatusBadge } from '@/components/release-status-badge';
import type { ReleaseStatus } from '@/types/release';
import { ArrowLeft, Disc3, Music, Calendar, Tag, Globe, FileText } from 'lucide-react';

export const Route = createFileRoute('/_authenticated/dashboard/releases/$key')({
  component: ReleaseDetail,
});

const statusTimeline: { status: ReleaseStatus; label: string }[] = [
  { status: 'draft', label: 'Черновик' },
  { status: 'awaiting_payment', label: 'Оплата' },
  { status: 'awaiting_contract', label: 'Договор' },
  { status: 'in_review', label: 'Проверка' },
  { status: 'approved', label: 'Одобрен' },
  { status: 'published', label: 'Опубликован' },
];

const statusOrder: Record<ReleaseStatus, number> = {
  draft: 0,
  awaiting_payment: 1,
  awaiting_contract: 2,
  in_review: 3,
  approved: 4,
  published: 5,
  rejected: -1,
};

function ReleaseDetail() {
  const { key } = Route.useParams();
  const { data: release, isLoading } = useRelease(key);
  const { data: tracks = [] } = useReleaseTracks(key);

  if (isLoading) {
    return (
      <div className="space-y-4">
        <div className="h-8 w-48 animate-pulse rounded bg-muted" />
        <div className="h-64 animate-pulse rounded bg-muted" />
      </div>
    );
  }

  if (!release) {
    return (
      <div className="flex flex-col items-center py-16">
        <p className="text-muted-foreground">Релиз не найден</p>
        <Link to="/dashboard">
          <Button variant="outline" className="mt-4 gap-1.5">
            <ArrowLeft className="h-4 w-4" />
            Назад
          </Button>
        </Link>
      </div>
    );
  }

  const currentStatusIndex = statusOrder[release.status];

  return (
    <div className="space-y-6">
      {/* Back link */}
      <Link
        to="/dashboard"
        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft className="h-3.5 w-3.5" />
        Назад к дашборду
      </Link>

      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-start">
        {release.cover_url ? (
          <img
            src={release.cover_url}
            alt={release.title}
            className="h-32 w-32 rounded-lg object-cover"
          />
        ) : (
          <div className="flex h-32 w-32 items-center justify-center rounded-lg bg-muted">
            <Disc3 className="h-10 w-10 text-muted-foreground/40" />
          </div>
        )}
        <div className="flex-1">
          <div className="flex items-start justify-between">
            <div>
              <h1 className="text-xl font-semibold">{release.title}</h1>
              <p className="text-sm text-muted-foreground">{release.artist_name}</p>
            </div>
            <ReleaseStatusBadge status={release.status} />
          </div>
          <div className="mt-3 flex flex-wrap gap-4 text-sm text-muted-foreground">
            <span className="flex items-center gap-1">
              <Tag className="h-3.5 w-3.5" />
              {release.type}
            </span>
            {release.genre && (
              <span className="flex items-center gap-1">
                <Music className="h-3.5 w-3.5" />
                {release.genre}
              </span>
            )}
            {release.language && (
              <span className="flex items-center gap-1">
                <Globe className="h-3.5 w-3.5" />
                {release.language}
              </span>
            )}
            {release.release_date && (
              <span className="flex items-center gap-1">
                <Calendar className="h-3.5 w-3.5" />
                {new Date(release.release_date).toLocaleDateString('ru-RU')}
              </span>
            )}
          </div>
        </div>
      </div>

      {/* Status timeline */}
      {release.status !== 'rejected' && (
        <Card>
          <CardHeader>
            <CardTitle className="text-sm">Статус</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              {statusTimeline.map((item, i) => (
                <div key={item.status} className="flex flex-1 items-center">
                  <div className="flex flex-1 flex-col items-center">
                    <div
                      className={`flex h-7 w-7 items-center justify-center rounded-full text-xs font-medium ${
                        i <= currentStatusIndex
                          ? 'bg-primary text-primary-foreground'
                          : 'bg-muted text-muted-foreground'
                      }`}
                    >
                      {i + 1}
                    </div>
                    <span className="mt-1 text-[10px] text-muted-foreground">{item.label}</span>
                  </div>
                  {i < statusTimeline.length - 1 && (
                    <div
                      className={`mx-1 h-px flex-1 ${
                        i < currentStatusIndex ? 'bg-primary' : 'bg-border'
                      }`}
                    />
                  )}
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}

      {release.status === 'rejected' && (
        <Card className="border-red-200 bg-red-50/50">
          <CardContent className="py-4">
            <p className="text-sm font-medium text-red-700">
              Релиз был отклонён модератором
            </p>
          </CardContent>
        </Card>
      )}

      {/* Description */}
      {release.description && (
        <Card>
          <CardHeader>
            <CardTitle className="text-sm">Описание</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground">{release.description}</p>
          </CardContent>
        </Card>
      )}

      {/* Track list */}
      <Card>
        <CardHeader>
          <CardTitle className="text-sm">
            Треки ({tracks.length})
          </CardTitle>
        </CardHeader>
        <CardContent>
          {tracks.length === 0 ? (
            <p className="py-4 text-center text-sm text-muted-foreground">Нет треков</p>
          ) : (
            <div className="space-y-1">
              {tracks.map((track, index) => (
                <div key={track.key}>
                  {index > 0 && <Separator />}
                  <div className="flex items-center gap-3 py-2">
                    <span className="w-6 text-center text-xs text-muted-foreground">
                      {track.track_number}
                    </span>
                    <FileText className="h-4 w-4 text-muted-foreground/50" />
                    <div className="flex-1">
                      <p className="text-sm font-medium">
                        {track.title || `Трек ${index + 1}`}
                      </p>
                    </div>
                    {track.duration_seconds != null && (
                      <span className="text-xs text-muted-foreground">
                        {Math.floor(track.duration_seconds / 60)}:{String(track.duration_seconds % 60).padStart(2, '0')}
                      </span>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      {/* Info */}
      <Card>
        <CardHeader>
          <CardTitle className="text-sm">Информация</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
            <div>
              <p className="text-xs text-muted-foreground">Создан</p>
              <p className="font-medium">
                {new Date(release.created_at).toLocaleDateString('ru-RU')}
              </p>
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Обновлён</p>
              <p className="font-medium">
                {new Date(release.updated_at).toLocaleDateString('ru-RU')}
              </p>
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Треков</p>
              <p className="font-medium">{tracks.length}</p>
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Тип</p>
              <p className="font-medium capitalize">{release.type}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
