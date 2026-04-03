import { createFileRoute } from '@tanstack/react-router';
import { useState } from 'react';
import { useAdminOrders } from '@/hooks/use-admin';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
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
import { ShoppingCart } from 'lucide-react';
import type { Order } from '@/types/admin';

export const Route = createFileRoute('/_admin/admin/orders')({
  component: AdminOrders,
});

const orderStatusConfig: Record<string, { label: string; className: string }> = {
  pending: { label: 'Ожидает', className: 'bg-amber-50 text-amber-700 border-amber-200' },
  paid: { label: 'Оплачен', className: 'bg-blue-50 text-blue-700 border-blue-200' },
  in_progress: { label: 'В работе', className: 'bg-purple-50 text-purple-700 border-purple-200' },
  completed: { label: 'Завершён', className: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
  cancelled: { label: 'Отменён', className: 'bg-gray-100 text-gray-500 border-gray-200' },
};

function AdminOrders() {
  const [statusFilter, setStatusFilter] = useState<string>('');
  const [page, setPage] = useState(1);

  const { data, isLoading } = useAdminOrders({
    status: statusFilter || undefined,
    page,
  });

  const orders = data?.data ?? [];
  const meta = data?.meta;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold tracking-tight">Заказы</h1>
        <p className="text-sm text-muted-foreground">Управление заказами и платежами</p>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="pt-4">
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
              <SelectItem value="pending">Ожидает</SelectItem>
              <SelectItem value="paid">Оплачен</SelectItem>
              <SelectItem value="in_progress">В работе</SelectItem>
              <SelectItem value="completed">Завершён</SelectItem>
              <SelectItem value="cancelled">Отменён</SelectItem>
            </SelectContent>
          </Select>
        </CardContent>
      </Card>

      {/* Table */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base">
            Заказы {meta?.total != null && `(${meta.total})`}
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : orders.length === 0 ? (
            <div className="flex flex-col items-center py-12">
              <ShoppingCart className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm text-muted-foreground">Нет заказов</p>
            </div>
          ) : (
            <>
              <div className="overflow-x-auto">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>ID</TableHead>
                      <TableHead>Статус</TableHead>
                      <TableHead className="hidden sm:table-cell">Заметки</TableHead>
                      <TableHead className="hidden sm:table-cell">Дата</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {orders.map((order) => {
                      const config = orderStatusConfig[order.status];
                      return (
                        <TableRow key={order.key}>
                          <TableCell className="text-sm font-medium font-mono">
                            {order.key}
                          </TableCell>
                          <TableCell>
                            <Badge variant="outline" className={config.className}>
                              {config.label}
                            </Badge>
                          </TableCell>
                          <TableCell className="hidden text-sm text-muted-foreground sm:table-cell">
                            {order.notes ?? '--'}
                          </TableCell>
                          <TableCell className="hidden text-sm text-muted-foreground sm:table-cell">
                            {new Date(order.created_at).toLocaleDateString('ru-RU')}
                          </TableCell>
                        </TableRow>
                      );
                    })}
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
    </div>
  );
}
