import { api } from '@/lib/api';
import type { Contract } from '@/types/contract';

interface JsonApiCollection<T> {
  data: Array<{
    id: string;
    type: string;
    attributes: T;
  }>;
}

function mapContract(item: { id: string; attributes: Omit<Contract, 'key'> }): Contract {
  return { key: item.id, ...item.attributes };
}

export async function fetchContracts(): Promise<Contract[]> {
  const response = await api.get<JsonApiCollection<Omit<Contract, 'key'>>>('/contracts');
  return response.data.data.map(mapContract);
}

export async function downloadContractPdf(key: string): Promise<void> {
  const response = await api.get(`/contracts/${key}/pdf`, {
    responseType: 'blob',
  });
  const url = window.URL.createObjectURL(new Blob([response.data as BlobPart]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', `contract-${key}.pdf`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  window.URL.revokeObjectURL(url);
}
