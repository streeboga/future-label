import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as servicesApi from '@/services/services';
import type { CreateServicePayload, UpdateServicePayload } from '@/types/service';

export function useServices() {
  return useQuery({
    queryKey: ['services'],
    queryFn: servicesApi.fetchServices,
  });
}

export function useCreateService() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload: CreateServicePayload) => servicesApi.createService(payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['services'] });
    },
  });
}

export function useUpdateService() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ key, payload }: { key: string; payload: UpdateServicePayload }) =>
      servicesApi.updateService(key, payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['services'] });
    },
  });
}

export function useDeleteService() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (key: string) => servicesApi.deleteService(key),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['services'] });
    },
  });
}
