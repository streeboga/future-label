import { createFileRoute, Link, useNavigate } from '@tanstack/react-router';
import { useReleases } from '@/hooks/use-releases';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { ReleaseStatusBadge } from '@/components/release-status-badge';
import { Plus, Disc3 } from 'lucide-react';

export const Route = createFileRoute('/_authenticated/dashboard/')({
  component: DashboardIndex,
});

function DashboardIndex() {
  const navigate = useNavigate();
  const { data: releasesData, isLoading: releasesLoading } = useReleases();

  const releases = releasesData?.data ?? [];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold tracking-tight">Дашборд</h1>
          <p className="text-sm text-muted-foreground">
            Управляйте своими релизами и контрактами
          </p>
        </div>
        <Link to="/dashboard/releases/new">
          <Button className="gap-1.5">
            <Plus className="h-4 w-4" />
            Новый релиз
          </Button>
        </Link>
      </div>

      {/* Release list */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base">
            Релизы {releasesData?.meta?.total != null && `(${releasesData.meta.total})`}
          </CardTitle>
        </CardHeader>
        <CardContent>
          {releasesLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : releases.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-12 text-center">
              <Disc3 className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm font-medium text-muted-foreground">Нет релизов</p>
              <p className="mb-4 text-xs text-muted-foreground">
                Создайте свой первый релиз
              </p>
              <Link to="/dashboard/releases/new">
                <Button size="sm" className="gap-1.5">
                  <Plus className="h-3.5 w-3.5" />
                  Новый релиз
                </Button>
              </Link>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-12"></TableHead>
                    <TableHead>Название</TableHead>
                    <TableHead className="hidden sm:table-cell">Артист</TableHead>
                    <TableHead>Статус</TableHead>
                    <TableHead className="hidden md:table-cell">Дата</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {releases.map((release) => (
                    <TableRow
                      key={release.key}
                      className="cursor-pointer"
                      onClick={() => void navigate({ to: '/dashboard/releases/$key', params: { key: release.key } })}
                    >
                      <TableCell>
                        {release.cover_url ? (
                          <img
                            src={release.cover_url}
                            alt=""
                            className="h-9 w-9 rounded object-cover"
                          />
                        ) : (
                          <div className="flex h-9 w-9 items-center justify-center rounded bg-muted">
                            <Disc3 className="h-4 w-4 text-muted-foreground" />
                          </div>
                        )}
                      </TableCell>
                      <TableCell className="text-sm font-medium">
                        {release.title}
                      </TableCell>
                      <TableCell className="hidden text-sm text-muted-foreground sm:table-cell">
                        {release.artist_name}
                      </TableCell>
                      <TableCell>
                        <ReleaseStatusBadge status={release.status} />
                      </TableCell>
                      <TableCell className="hidden text-sm text-muted-foreground md:table-cell">
                        {new Date(release.created_at).toLocaleDateString('ru-RU')}
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
