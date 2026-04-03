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

function mapArtist(item: { id: string; attributes: Record<string, unknown> }): Artist {
  const attrs = item.attributes;
  return {
    key: item.id,
    name: attrs.name as string,
    email: attrs.email as string,
    role: attrs.role as string,
    stage_name: (attrs.stage_name as string | null) ?? null,
    created_at: attrs.created_at as string,
  };
}

function mapRelease(item: { id: string; attributes: Record<string, unknown> }): Release {
  const attrs = item.attributes;
  return {
    key: item.id,
    title: attrs.title as string,
    artist_name: (attrs.artist_name as string | null) ?? '',
    type: attrs.type as Release['type'],
    status: attrs.status as Release['status'],
    genre: (attrs.genre as string | null) ?? null,
    language: (attrs.language as string | null) ?? null,
    description: (attrs.description as string | null) ?? null,
    release_date: (attrs.release_date as string | null) ?? null,
    cover_url: (attrs.cover_url as string | null) ?? null,
    reject_reason: (attrs.reject_reason as string | null) ?? null,
    created_at: attrs.created_at as string,
    updated_at: attrs.updated_at as string,
  };
}

function mapOrder(item: {
  id: string;
  attributes: Record<string, unknown>;
  relationships?: Record<string, { data?: { id: string; type: string; attributes?: Record<string, unknown> } }>;
}): Order {
  const attrs = item.attributes;
  return {
    key: item.id,
    status: attrs.status as Order['status'],
    notes: (attrs.notes as string | null) ?? null,
    created_at: attrs.created_at as string,
  };
}

export async function fetchAdminMetrics(): Promise<AdminMetrics> {
  const response = await api.get('/admin/dashboard');
  return response.data.data.attributes;
}

export async function fetchArtists(params?: {
  search?: string;
  page?: number;
}): Promise<{ data: Artist[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.search) searchParams.set('search', params.search);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/admin/users?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapArtist),
    meta: response.data.meta,
  };
}

export async function fetchArtist(key: string): Promise<Artist> {
  const response = await api.get<JsonApiResource<Record<string, unknown>>>(`/admin/users/${key}`);
  return mapArtist(response.data.data);
}

export async function fetchAdminReleases(params?: {
  status?: string;
  search?: string;
  page?: number;
}): Promise<{ data: Release[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.status) searchParams.set('filter[status]', params.status);
  if (params?.search) searchParams.set('search', params.search);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/admin/releases?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapRelease),
    meta: response.data.meta,
  };
}

export async function moderateRelease(key: string, action: ModerationAction): Promise<Release> {
  const response = await api.patch<JsonApiResource<Record<string, unknown>>>(
    `/admin/releases/${key}/status`,
    { action: action.action, comment: action.comment ?? null }
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

  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/orders?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map((item) => mapOrder(item as Parameters<typeof mapOrder>[0])),
    meta: response.data.meta,
  };
}
