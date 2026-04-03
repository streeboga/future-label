import { api } from '@/lib/api';
import type { Service, CreateServicePayload, UpdateServicePayload } from '@/types/service';

interface JsonApiResource<T> {
  data: {
    id: string;
    type: string;
    attributes: T;
  };
}

interface JsonApiCollection<T> {
  data: Array<{
    id: string;
    type: string;
    attributes: T;
  }>;
}

function mapService(item: { id: string; attributes: Omit<Service, 'key'> }): Service {
  return { key: item.id, ...item.attributes };
}

export async function fetchServices(): Promise<Service[]> {
  const response = await api.get<JsonApiCollection<Omit<Service, 'key'>>>('/services');
  return response.data.data.map(mapService);
}

export async function fetchService(key: string): Promise<Service> {
  const response = await api.get<JsonApiResource<Omit<Service, 'key'>>>(`/services/${key}`);
  return mapService(response.data.data);
}

export async function createService(payload: CreateServicePayload): Promise<Service> {
  const response = await api.post<JsonApiResource<Omit<Service, 'key'>>>('/admin/services', {
    data: {
      type: 'services',
      attributes: payload,
    },
  });
  return mapService(response.data.data);
}

export async function updateService(key: string, payload: UpdateServicePayload): Promise<Service> {
  const response = await api.patch<JsonApiResource<Omit<Service, 'key'>>>(`/admin/services/${key}`, {
    data: {
      type: 'services',
      id: key,
      attributes: payload,
    },
  });
  return mapService(response.data.data);
}

export async function deleteService(key: string): Promise<void> {
  await api.delete(`/admin/services/${key}`);
}
