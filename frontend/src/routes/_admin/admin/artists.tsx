import { createFileRoute } from '@tanstack/react-router';
import { useState } from 'react';
import { useAdminArtists } from '@/hooks/use-admin';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { useAdminArtist } from '@/hooks/use-admin';
import { useAdminReleases } from '@/hooks/use-admin';
import { ReleaseStatusBadge } from '@/components/release-status-badge';
import { Search, Users, Disc3 } from 'lucide-react';

export const Route = createFileRoute('/_admin/admin/artists')({
  component: AdminArtists,
});

function AdminArtists() {
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [selectedArtist, setSelectedArtist] = useState<string | null>(null);

  const { data, isLoading } = useAdminArtists({
    search: search || undefined,
    page,
  });

  const artists = data?.data ?? [];
  const meta = data?.meta;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold tracking-tight">Артисты</h1>
        <p className="text-sm text-muted-foreground">Управление пользователями платформы</p>
      </div>

      {/* Search */}
      <Card>
        <CardContent className="pt-4">
          <div className="relative">
            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Поиск по имени или email..."
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
                setPage(1);
              }}
              className="pl-9"
            />
          </div>
        </CardContent>
      </Card>

      {/* Table */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base">
            Артисты {meta?.total != null && `(${meta.total})`}
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : artists.length === 0 ? (
            <div className="flex flex-col items-center py-12">
              <Users className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm text-muted-foreground">Нет артистов</p>
            </div>
          ) : (
            <>
              <div className="overflow-x-auto">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Имя</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead className="text-center">Релизы</TableHead>
                      <TableHead className="hidden sm:table-cell">Регистрация</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {artists.map((artist) => (
                      <TableRow
                        key={artist.key}
                        className="cursor-pointer"
                        onClick={() => setSelectedArtist(artist.key)}
                      >
                        <TableCell>
                          <div className="flex items-center gap-2">
                            <div className="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                              {artist.name.charAt(0).toUpperCase()}
                            </div>
                            <span className="text-sm font-medium">{artist.name}</span>
                          </div>
                        </TableCell>
                        <TableCell className="text-sm text-muted-foreground">
                          {artist.email}
                        </TableCell>
                        <TableCell className="text-center text-sm">
                          {artist.releases_count}
                        </TableCell>
                        <TableCell className="hidden text-sm text-muted-foreground sm:table-cell">
                          {new Date(artist.created_at).toLocaleDateString('ru-RU')}
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>

              {meta && meta.last_page > 1 && (
                <div className="mt-4 flex items-center justify-center gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    disabled={page <= 1}
                    onClick={() => setPage((p) => p - 1)}
                  >
                    Назад
                  </Button>
                  <span className="text-sm text-muted-foreground">
                    {page} / {meta.last_page}
                  </span>
                  <Button
                    variant="outline"
                    size="sm"
                    disabled={page >= meta.last_page}
                    onClick={() => setPage((p) => p + 1)}
                  >
                    Далее
                  </Button>
                </div>
              )}
            </>
          )}
        </CardContent>
      </Card>

      {/* Artist detail modal */}
      {selectedArtist && (
        <ArtistDetailDialog
          artistKey={selectedArtist}
          onClose={() => setSelectedArtist(null)}
        />
      )}
    </div>
  );
}

function ArtistDetailDialog({
  artistKey,
  onClose,
}: {
  artistKey: string;
  onClose: () => void;
}) {
  const { data: artist, isLoading: artistLoading } = useAdminArtist(artistKey);
  const { data: releasesData, isLoading: releasesLoading } = useAdminReleases({
    search: artist?.name,
  });

  const releases = releasesData?.data ?? [];

  return (
    <Dialog open onOpenChange={(open) => !open && onClose()}>
      <DialogContent className="max-w-lg">
        <DialogHeader>
          <DialogTitle>Профиль артиста</DialogTitle>
        </DialogHeader>
        {artistLoading ? (
          <div className="space-y-3">
            <div className="h-6 w-32 animate-pulse rounded bg-muted" />
            <div className="h-4 w-48 animate-pulse rounded bg-muted" />
          </div>
        ) : artist ? (
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-lg font-semibold text-primary">
                {artist.name.charAt(0).toUpperCase()}
              </div>
              <div>
                <p className="font-medium">{artist.name}</p>
                <p className="text-sm text-muted-foreground">{artist.email}</p>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-3 text-sm">
              <div>
                <p className="text-xs text-muted-foreground">Релизов</p>
                <p className="font-medium">{artist.releases_count}</p>
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Регистрация</p>
                <p className="font-medium">
                  {new Date(artist.created_at).toLocaleDateString('ru-RU')}
                </p>
              </div>
            </div>

            {/* Release history */}
            <div>
              <p className="mb-2 text-sm font-medium">История релизов</p>
              {releasesLoading ? (
                <div className="space-y-2">
                  {Array.from({ length: 3 }).map((_, i) => (
                    <div key={i} className="h-8 animate-pulse rounded bg-muted" />
                  ))}
                </div>
              ) : releases.length === 0 ? (
                <div className="flex flex-col items-center py-6">
                  <Disc3 className="mb-2 h-6 w-6 text-muted-foreground/40" />
                  <p className="text-xs text-muted-foreground">Нет релизов</p>
                </div>
              ) : (
                <div className="max-h-48 space-y-1 overflow-y-auto">
                  {releases.map((release) => (
                    <div
                      key={release.key}
                      className="flex items-center justify-between rounded border px-3 py-1.5"
                    >
                      <span className="text-sm">{release.title}</span>
                      <ReleaseStatusBadge status={release.status} />
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        ) : (
          <p className="text-sm text-muted-foreground">Артист не найден</p>
        )}
      </DialogContent>
    </Dialog>
  );
}
