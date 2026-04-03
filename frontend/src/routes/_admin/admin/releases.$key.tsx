import { createFileRoute, Link } from '@tanstack/react-router';
import { useState } from 'react';
import { useRelease, useReleaseTracks } from '@/hooks/use-releases';
import { useModerateRelease } from '@/hooks/use-admin';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import { ReleaseStatusBadge } from '@/components/release-status-badge';
import {
  ArrowLeft,
  Disc3,
  Music,
  Calendar,
  Tag,
  Globe,
  CheckCircle2,
  XCircle,
  Play,
  Loader2,
} from 'lucide-react';

export const Route = createFileRoute('/_admin/admin/releases/$key')({
  component: AdminReleaseDetail,
});

function AdminReleaseDetail() {
  const { key } = Route.useParams();
  const { data: release, isLoading } = useRelease(key);
  const { data: tracks = [] } = useReleaseTracks(key);
  const moderateRelease = useModerateRelease();

  const [moderationModal, setModerationModal] = useState<'approve' | 'reject' | null>(null);
  const [comment, setComment] = useState('');
  const [playingTrack, setPlayingTrack] = useState<string | null>(null);

  const handleModerate = async () => {
    if (!moderationModal || !release) return;
    await moderateRelease.mutateAsync({
      key: release.key,
      action: { action: moderationModal, comment: comment || undefined },
    });
    setModerationModal(null);
    setComment('');
  };

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
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <Link
        to="/admin/releases"
        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft className="h-3.5 w-3.5" />
        Назад к релизам
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

          {/* Moderation actions */}
          {release.status === 'in_review' && (
            <div className="mt-4 flex gap-2">
              <Button
                size="sm"
                className="gap-1.5"
                onClick={() => setModerationModal('approve')}
              >
                <CheckCircle2 className="h-4 w-4" />
                Одобрить
              </Button>
              <Button
                size="sm"
                variant="destructive"
                className="gap-1.5"
                onClick={() => setModerationModal('reject')}
              >
                <XCircle className="h-4 w-4" />
                Отклонить
              </Button>
            </div>
          )}
        </div>
      </div>

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

      {/* Track list with audio player */}
      <Card>
        <CardHeader>
          <CardTitle className="text-sm">Треки ({tracks.length})</CardTitle>
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
                      {track.position}
                    </span>
                    {track.file_url ? (
                      <button
                        onClick={() =>
                          setPlayingTrack(playingTrack === track.key ? null : track.key)
                        }
                        className="rounded p-1 text-muted-foreground hover:text-primary"
                      >
                        <Play className="h-4 w-4" />
                      </button>
                    ) : (
                      <div className="w-6" />
                    )}
                    <div className="flex-1">
                      <p className="text-sm font-medium">
                        {track.title || track.file_name || `Трек ${index + 1}`}
                      </p>
                      {playingTrack === track.key && track.file_url && (
                        <audio
                          src={track.file_url}
                          controls
                          autoPlay
                          className="mt-2 w-full"
                          onEnded={() => setPlayingTrack(null)}
                        />
                      )}
                    </div>
                    {track.duration && (
                      <span className="text-xs text-muted-foreground">
                        {Math.floor(track.duration / 60)}:
                        {String(track.duration % 60).padStart(2, '0')}
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
              <p className="font-medium">{release.tracks_count}</p>
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Тип</p>
              <p className="font-medium capitalize">{release.type}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Moderation modal */}
      <Dialog
        open={moderationModal !== null}
        onOpenChange={(open) => {
          if (!open) {
            setModerationModal(null);
            setComment('');
          }
        }}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {moderationModal === 'approve' ? 'Одобрить релиз' : 'Отклонить релиз'}
            </DialogTitle>
          </DialogHeader>
          <Textarea
            placeholder="Комментарий..."
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            rows={3}
          />
          <DialogFooter>
            <Button variant="outline" onClick={() => setModerationModal(null)}>
              Отмена
            </Button>
            <Button
              onClick={() => void handleModerate()}
              disabled={moderateRelease.isPending}
              variant={moderationModal === 'reject' ? 'destructive' : 'default'}
            >
              {moderateRelease.isPending && <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />}
              {moderationModal === 'approve' ? 'Одобрить' : 'Отклонить'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
