import { createFileRoute } from '@tanstack/react-router';
import { useNotifications, useMarkAsRead } from '@/hooks/use-notifications';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Bell, Check, Loader2 } from 'lucide-react';

export const Route = createFileRoute('/_authenticated/dashboard/notifications')({
  component: NotificationsPage,
});

function NotificationsPage() {
  const { data, isLoading } = useNotifications();
  const markAsRead = useMarkAsRead();
  const notifications = data?.data ?? [];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold tracking-tight">Уведомления</h1>
        <p className="text-sm text-muted-foreground">
          История уведомлений о ваших релизах и контрактах
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Все уведомления</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="h-16 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : notifications.length === 0 ? (
            <div className="flex flex-col items-center py-12">
              <Bell className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm text-muted-foreground">Нет уведомлений</p>
            </div>
          ) : (
            <div className="space-y-2">
              {notifications.map((notification) => (
                <div
                  key={notification.key}
                  className={`flex items-start gap-3 rounded-lg border p-3 transition-colors ${
                    notification.read_at
                      ? 'bg-background'
                      : 'border-primary/20 bg-primary/5'
                  }`}
                >
                  <div
                    className={`mt-0.5 h-2 w-2 shrink-0 rounded-full ${
                      notification.read_at ? 'bg-transparent' : 'bg-primary'
                    }`}
                  />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium">{notification.title}</p>
                    <p className="mt-0.5 text-xs text-muted-foreground line-clamp-2">
                      {notification.body}
                    </p>
                    <p className="mt-1 text-[10px] text-muted-foreground">
                      {new Date(notification.created_at).toLocaleString('ru-RU')}
                    </p>
                  </div>
                  {!notification.read_at && (
                    <Button
                      variant="ghost"
                      size="sm"
                      className="h-7 w-7 shrink-0 p-0"
                      disabled={markAsRead.isPending}
                      onClick={() => markAsRead.mutate(notification.key)}
                    >
                      {markAsRead.isPending ? (
                        <Loader2 className="h-3.5 w-3.5 animate-spin" />
                      ) : (
                        <Check className="h-3.5 w-3.5" />
                      )}
                    </Button>
                  )}
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
