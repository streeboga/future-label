import { api } from '@/lib/api';
import type { Artist, AdminMetrics, ModerationAction, Order } from '@/types/admin';
import type { Release } from '@/types/release';

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
  meta?: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
}

function mapArtist(item: { id: string; attributes: Omit<Artist, 'key'> }): Artist {
  return { key: item.id, ...item.attributes };
}

function mapRelease(item: { id: string; attributes: Omit<Release, 'key'> }): Release {
  return { key: item.id, ...item.attributes };
}

function mapOrder(item: { id: string; attributes: Omit<Order, 'key'> }): Order {
  return { key: item.id, ...item.attributes };
}

export async function fetchAdminMetrics(): Promise<AdminMetrics> {
  const response = await api.get<{ data: AdminMetrics }>('/admin/metrics');
  return response.data.data;
}

export async function fetchArtists(params?: {
  search?: string;
  page?: number;
}): Promise<{ data: Artist[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.search) searchParams.set('filter[search]', params.search);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Omit<Artist, 'key'>>>(
    `/admin/artists?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapArtist),
    meta: response.data.meta,
  };
}

export async function fetchArtist(key: string): Promise<Artist> {
  const response = await api.get<JsonApiResource<Omit<Artist, 'key'>>>(`/admin/artists/${key}`);
  return mapArtist(response.data.data);
}

export async function fetchAdminReleases(params?: {
  status?: string;
  search?: string;
  page?: number;
}): Promise<{ data: Release[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.status) searchParams.set('filter[status]', params.status);
  if (params?.search) searchParams.set('filter[search]', params.search);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Omit<Release, 'key'>>>(
    `/admin/releases?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapRelease),
    meta: response.data.meta,
  };
}

export async function moderateRelease(key: string, action: ModerationAction): Promise<Release> {
  const response = await api.post<JsonApiResource<Omit<Release, 'key'>>>(
    `/admin/releases/${key}/moderate`,
    { data: { type: 'moderation', attributes: action } }
  );
  return mapRelease(response.data.data);
}

export async function fetchOrders(params?: {
  status?: string;
  page?: number;
}): Promise<{ data: Order[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.status) searchParams.set('filter[status]', params.status);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Omit<Order, 'key'>>>(
    `/admin/orders?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapOrder),
    meta: response.data.meta,
  };
}

export async function exportReleasesJson(): Promise<void> {
  const response = await api.get('/admin/releases/export', {
    responseType: 'blob',
  });
  const url = window.URL.createObjectURL(new Blob([response.data as BlobPart]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', 'releases-export.json');
  document.body.appendChild(link);
  link.click();
  link.remove();
  window.URL.revokeObjectURL(url);
}
