import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as contractsApi from '@/services/contracts';

export function useContracts() {
  return useQuery({
    queryKey: ['contracts'],
    queryFn: contractsApi.fetchContracts,
  });
}

export function useDownloadContract() {
  return useMutation({
    mutationFn: (key: string) => contractsApi.downloadContractPdf(key),
  });
}

export function useAcceptContract() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (key: string) => contractsApi.acceptContract(key),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['contracts'] });
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}
