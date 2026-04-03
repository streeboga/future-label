import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as notificationsApi from '@/services/notifications';

export function useNotifications(params?: { page?: number }) {
  return useQuery({
    queryKey: ['notifications', params],
    queryFn: () => notificationsApi.fetchNotifications(params),
  });
}

export function useMarkAsRead() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (key: string) => notificationsApi.markAsRead(key),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['notifications'] });
    },
  });
}
