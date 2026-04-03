import { api } from '@/lib/api';
import type {
  Release,
  Track,
  CreateReleasePayload,
  UpdateReleasePayload,
} from '@/types/release';

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

function mapTrack(item: { id: string; attributes: Record<string, unknown> }): Track {
  const attrs = item.attributes;
  return {
    key: item.id,
    title: attrs.title as string,
    track_number: attrs.track_number as number,
    duration_seconds: (attrs.duration_seconds as number | null) ?? null,
    file_url: (attrs.file_url as string | null) ?? null,
    format: attrs.format as string,
    file_size: (attrs.file_size as number | null) ?? null,
    authors: (attrs.authors as string[] | null) ?? null,
    composers: (attrs.composers as string[] | null) ?? null,
    lyrics: (attrs.lyrics as string | null) ?? null,
    isrc: (attrs.isrc as string | null) ?? null,
    created_at: attrs.created_at as string,
    updated_at: attrs.updated_at as string,
  };
}

export async function fetchReleases(params?: {
  status?: string;
  search?: string;
  page?: number;
}): Promise<{ data: Release[]; meta?: JsonApiCollection<unknown>['meta'] }> {
  const searchParams = new URLSearchParams();
  if (params?.status) searchParams.set('filter[status]', params.status);
  if (params?.search) searchParams.set('filter[search]', params.search);
  if (params?.page) searchParams.set('page[number]', String(params.page));

  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/releases?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapRelease),
    meta: response.data.meta,
  };
}

export async function fetchRelease(key: string): Promise<Release> {
  const response = await api.get<JsonApiResource<Record<string, unknown>>>(`/releases/${key}`);
  return mapRelease(response.data.data);
}

export async function createRelease(payload: CreateReleasePayload): Promise<Release> {
  const response = await api.post<JsonApiResource<Record<string, unknown>>>('/releases', {
    data: {
      type: 'releases',
      attributes: payload,
    },
  });
  return mapRelease(response.data.data);
}

export async function updateRelease(key: string, payload: UpdateReleasePayload): Promise<Release> {
  const response = await api.patch<JsonApiResource<Record<string, unknown>>>(`/releases/${key}`, {
    data: {
      type: 'releases',
      id: key,
      attributes: payload,
    },
  });
  return mapRelease(response.data.data);
}

export async function deleteRelease(key: string): Promise<void> {
  await api.delete(`/releases/${key}`);
}

export async function submitRelease(key: string): Promise<Release> {
  const response = await api.post<JsonApiResource<Record<string, unknown>>>(`/releases/${key}/submit`);
  return mapRelease(response.data.data);
}

export async function fetchReleaseTracks(releaseKey: string): Promise<Track[]> {
  const response = await api.get<JsonApiCollection<Record<string, unknown>>>(
    `/releases/${releaseKey}/tracks`
  );
  return response.data.data.map(mapTrack);
}

export async function uploadTrack(releaseKey: string, file: File, position: number): Promise<Track> {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('position', String(position));

  const response = await api.post<JsonApiResource<Record<string, unknown>>>(
    `/releases/${releaseKey}/tracks`,
    formData,
    { headers: { 'Content-Type': 'multipart/form-data' } }
  );
  return mapTrack(response.data.data);
}

export async function deleteTrack(releaseKey: string, trackKey: string): Promise<void> {
  await api.delete(`/releases/${releaseKey}/tracks/${trackKey}`);
}
