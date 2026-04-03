import { api } from '@/lib/api';

export interface Notification {
  key: string;
  type: string;
  title: string;
  body: string;
  read_at: string | null;
  data: Record<string, unknown> | null;
  created_at: string;
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

function mapNotification(item: { id: string; attributes: Record<string, unknown> }): Notification {
  const attrs = item.attributes;
  return {
    key: item.id,
    type: attrs.type as string,
    title: attrs.title as string,
    body: attrs.body as string,
    read_at: (attrs.read_at as string | null) ?? null,
    data: (attrs.data as Record<string, unknown> | null) ?? null,
    created_at: attrs.created_at as string,
  };
}

export async function fetchNotifications(params?: {
  page?: number;
}): Promise<{ data: Notification[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/notifications?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapNotification),
    meta: response.data.meta,
  };
}

export async function markAsRead(key: string): Promise<void> {
  await api.patch(`/notifications/${key}/read`);
}
