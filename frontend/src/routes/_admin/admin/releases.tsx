import { createFileRoute, Link } from '@tanstack/react-router';
import { useState } from 'react';
import { useAdminReleases, useModerateRelease } from '@/hooks/use-admin';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
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
  DialogFooter,
} from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { ReleaseStatusBadge } from '@/components/release-status-badge';
import { CheckCircle2, XCircle, Search, Loader2, Globe } from 'lucide-react';

export const Route = createFileRoute('/_admin/admin/releases')({
  component: AdminReleases,
});

function AdminReleases() {
  const [statusFilter, setStatusFilter] = useState<string>('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  const { data, isLoading } = useAdminReleases({
    status: statusFilter || undefined,
    search: search || undefined,
    page,
  });

  const moderateRelease = useModerateRelease();

  const [moderationModal, setModerationModal] = useState<{
    key: string;
    action: 'approve' | 'reject' | 'publish';
  } | null>(null);
  const [moderationComment, setModerationComment] = useState('');

  const releases = data?.data ?? [];
  const meta = data?.meta;

  const handleModerate = async () => {
    if (!moderationModal) return;
    await moderateRelease.mutateAsync({
      key: moderationModal.key,
      action: {
        action: moderationModal.action,
        comment: moderationComment || undefined,
      },
    });
    setModerationModal(null);
    setModerationComment('');
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold tracking-tight">Управление релизами</h1>
          <p className="text-sm text-muted-foreground">Модерация и управление контентом</p>
        </div>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="pt-4">
          <div className="flex flex-col gap-3 sm:flex-row">
            <div className="relative flex-1">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Поиск по названию или артисту..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPage(1);
                }}
                className="pl-9"
              />
            </div>
            <Select
              value={statusFilter}
              onValueChange={(val) => {
                setStatusFilter(val === 'all' ? '' : val);
                setPage(1);
              }}
            >
              <SelectTrigger className="w-full sm:w-40">
                <SelectValue placeholder="Все статусы" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Все статусы</SelectItem>
                <SelectItem value="draft">Черновик</SelectItem>
                <SelectItem value="in_review">На проверке</SelectItem>
                <SelectItem value="approved">Одобрен</SelectItem>
                <SelectItem value="published">Опубликован</SelectItem>
                <SelectItem value="rejected">Отклонён</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Table */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base">
            Релизы {meta?.total != null && `(${meta.total})`}
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : releases.length === 0 ? (
            <p className="py-8 text-center text-sm text-muted-foreground">Нет релизов</p>
          ) : (
            <>
              <div className="overflow-x-auto">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Название</TableHead>
                      <TableHead>Артист</TableHead>
                      <TableHead>Тип</TableHead>
                      <TableHead>Статус</TableHead>
                      <TableHead className="hidden md:table-cell">Дата</TableHead>
                      <TableHead className="w-24">Действия</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {releases.map((release) => (
                      <TableRow key={release.key}>
                        <TableCell>
                          <Link
                            to="/admin/releases/$key"
                            params={{ key: release.key }}
                            className="text-sm font-medium hover:underline"
                          >
                            {release.title}
                          </Link>
                        </TableCell>
                        <TableCell className="text-sm text-muted-foreground">
                          {release.artist_name}
                        </TableCell>
                        <TableCell className="text-sm capitalize text-muted-foreground">
                          {release.type}
                        </TableCell>
                        <TableCell>
                          <ReleaseStatusBadge status={release.status} />
                        </TableCell>
                        <TableCell className="hidden text-sm text-muted-foreground md:table-cell">
                          {new Date(release.created_at).toLocaleDateString('ru-RU')}
                        </TableCell>
                        <TableCell>
                          {release.status === 'in_review' && (
                            <div className="flex gap-1">
                              <Button
                                variant="ghost"
                                size="sm"
                                className="h-7 w-7 p-0 text-emerald-600 hover:text-emerald-700"
                                onClick={() =>
                                  setModerationModal({ key: release.key, action: 'approve' })
                                }
                              >
                                <CheckCircle2 className="h-4 w-4" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                className="h-7 w-7 p-0 text-red-600 hover:text-red-700"
                                onClick={() =>
                                  setModerationModal({ key: release.key, action: 'reject' })
                                }
                              >
                                <XCircle className="h-4 w-4" />
                              </Button>
                            </div>
                          )}
                          {release.status === 'approved' && (
                            <Button
                              variant="ghost"
                              size="sm"
                              className="h-7 w-7 p-0 text-blue-600 hover:text-blue-700"
                              onClick={() =>
                                setModerationModal({ key: release.key, action: 'publish' })
                              }
                            >
                              <Globe className="h-4 w-4" />
                            </Button>
                          )}
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>

              {/* Pagination */}
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

      {/* Moderation modal */}
      <Dialog
        open={moderationModal !== null}
        onOpenChange={(open) => {
          if (!open) {
            setModerationModal(null);
            setModerationComment('');
          }
        }}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {moderationModal?.action === 'approve' && 'Одобрить релиз'}
              {moderationModal?.action === 'reject' && 'Отклонить релиз'}
              {moderationModal?.action === 'publish' && 'Опубликовать релиз'}
            </DialogTitle>
          </DialogHeader>
          {moderationModal?.action !== 'publish' ? (
            <div className="space-y-3">
              <Textarea
                placeholder="Комментарий (необязательно)..."
                value={moderationComment}
                onChange={(e) => setModerationComment(e.target.value)}
                rows={3}
              />
            </div>
          ) : (
            <p className="text-sm text-muted-foreground">
              Релиз будет опубликован и станет доступен на площадках. Продолжить?
            </p>
          )}
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => {
                setModerationModal(null);
                setModerationComment('');
              }}
            >
              Отмена
            </Button>
            <Button
              onClick={() => void handleModerate()}
              disabled={moderateRelease.isPending}
              variant={moderationModal?.action === 'reject' ? 'destructive' : 'default'}
            >
              {moderateRelease.isPending && <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />}
              {moderationModal?.action === 'approve' && 'Одобрить'}
              {moderationModal?.action === 'reject' && 'Отклонить'}
              {moderationModal?.action === 'publish' && 'Опубликовать'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
