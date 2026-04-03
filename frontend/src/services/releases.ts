import { api } from '@/lib/api';
import type {
  Release,
  Track,
  CreateReleasePayload,
  UpdateReleasePayload,
  ReleaseMetrics,
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

function mapRelease(item: { id: string; attributes: Omit<Release, 'key'> }): Release {
  return { key: item.id, ...item.attributes };
}

function mapTrack(item: { id: string; attributes: Omit<Track, 'key'> }): Track {
  return { key: item.id, ...item.attributes };
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

  const response = await api.get<JsonApiCollection<Omit<Release, 'key'>>>(
    `/releases?${searchParams.toString()}`
  );
  return {
    data: response.data.data.map(mapRelease),
    meta: response.data.meta,
  };
}

export async function fetchRelease(key: string): Promise<Release> {
  const response = await api.get<JsonApiResource<Omit<Release, 'key'>>>(`/releases/${key}`);
  return mapRelease(response.data.data);
}

export async function createRelease(payload: CreateReleasePayload): Promise<Release> {
  const response = await api.post<JsonApiResource<Omit<Release, 'key'>>>('/releases', {
    data: {
      type: 'releases',
      attributes: payload,
    },
  });
  return mapRelease(response.data.data);
}

export async function updateRelease(key: string, payload: UpdateReleasePayload): Promise<Release> {
  const response = await api.patch<JsonApiResource<Omit<Release, 'key'>>>(`/releases/${key}`, {
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
  const response = await api.post<JsonApiResource<Omit<Release, 'key'>>>(`/releases/${key}/submit`);
  return mapRelease(response.data.data);
}

export async function fetchReleaseTracks(releaseKey: string): Promise<Track[]> {
  const response = await api.get<JsonApiCollection<Omit<Track, 'key'>>>(
    `/releases/${releaseKey}/tracks`
  );
  return response.data.data.map(mapTrack);
}

export async function uploadTrack(releaseKey: string, file: File, position: number): Promise<Track> {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('position', String(position));

  const response = await api.post<JsonApiResource<Omit<Track, 'key'>>>(
    `/releases/${releaseKey}/tracks`,
    formData,
    { headers: { 'Content-Type': 'multipart/form-data' } }
  );
  return mapTrack(response.data.data);
}

export async function deleteTrack(releaseKey: string, trackKey: string): Promise<void> {
  await api.delete(`/releases/${releaseKey}/tracks/${trackKey}`);
}

export async function uploadCover(releaseKey: string, file: File): Promise<Release> {
  const formData = new FormData();
  formData.append('cover', file);

  const response = await api.post<JsonApiResource<Omit<Release, 'key'>>>(
    `/releases/${releaseKey}/cover`,
    formData,
    { headers: { 'Content-Type': 'multipart/form-data' } }
  );
  return mapRelease(response.data.data);
}

export async function fetchReleaseMetrics(): Promise<ReleaseMetrics> {
  const response = await api.get<{ data: ReleaseMetrics }>('/releases/metrics');
  return response.data.data;
}
