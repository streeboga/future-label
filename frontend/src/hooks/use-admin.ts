import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as adminApi from '@/services/admin';
import type { ModerationAction } from '@/types/admin';

export function useAdminMetrics() {
  return useQuery({
    queryKey: ['admin', 'metrics'],
    queryFn: adminApi.fetchAdminMetrics,
  });
}

export function useAdminArtists(params?: { search?: string; page?: number }) {
  return useQuery({
    queryKey: ['admin', 'artists', params],
    queryFn: () => adminApi.fetchArtists(params),
  });
}

export function useAdminArtist(key: string) {
  return useQuery({
    queryKey: ['admin', 'artists', key],
    queryFn: () => adminApi.fetchArtist(key),
    enabled: !!key,
  });
}

export function useAdminReleases(params?: { status?: string; search?: string; page?: number }) {
  return useQuery({
    queryKey: ['admin', 'releases', params],
    queryFn: () => adminApi.fetchAdminReleases(params),
  });
}

export function useModerateRelease() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ key, action }: { key: string; action: ModerationAction }) =>
      adminApi.moderateRelease(key, action),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['admin', 'releases'] });
      void queryClient.invalidateQueries({ queryKey: ['admin', 'metrics'] });
    },
  });
}

export function useAdminOrders(params?: { status?: string; page?: number }) {
  return useQuery({
    queryKey: ['admin', 'orders', params],
    queryFn: () => adminApi.fetchOrders(params),
  });
}

