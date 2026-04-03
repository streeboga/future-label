import { useQuery, useMutation } from '@tanstack/react-query';
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
