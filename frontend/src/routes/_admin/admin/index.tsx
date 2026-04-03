import { createFileRoute } from '@tanstack/react-router';
import { useAdminMetrics } from '@/hooks/use-admin';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, Disc3, DollarSign, Clock } from 'lucide-react';

export const Route = createFileRoute('/_admin/admin/')({
  component: AdminDashboard,
});

function AdminDashboard() {
  const { data: metrics, isLoading } = useAdminMetrics();

  const cards = [
    {
      title: 'Артисты',
      value: metrics?.total_artists ?? 0,
      icon: Users,
      color: 'text-blue-600',
    },
    {
      title: 'Релизы (месяц)',
      value: metrics?.releases_this_month ?? 0,
      icon: Disc3,
      color: 'text-emerald-600',
    },
    {
      title: 'Выручка',
      value: metrics?.revenue
        ? `${metrics.revenue.toLocaleString('ru-RU')} RUB`
        : '0 RUB',
      icon: DollarSign,
      color: 'text-violet-600',
      isString: true,
    },
    {
      title: 'На модерации',
      value: metrics?.pending_moderation ?? 0,
      icon: Clock,
      color: 'text-amber-600',
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold tracking-tight">Панель администратора</h1>
        <p className="text-sm text-muted-foreground">Обзор платформы</p>
      </div>

      <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
        {cards.map((card) => (
          <Card key={card.title} className="gap-3 py-4">
            <CardHeader className="pb-0">
              <div className="flex items-center justify-between">
                <CardTitle className="text-xs font-medium text-muted-foreground">
                  {card.title}
                </CardTitle>
                <card.icon className={`h-4 w-4 ${card.color}`} />
              </div>
            </CardHeader>
            <CardContent>
              {isLoading ? (
                <div className="h-7 w-16 animate-pulse rounded bg-muted" />
              ) : (
                <p className={`text-2xl font-semibold ${card.color}`}>
                  {'isString' in card ? card.value : card.value}
                </p>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
