export type ReleaseType = 'single' | 'ep' | 'album';

export type ReleaseStatus = 'draft' | 'in_review' | 'approved' | 'published' | 'rejected';

export interface Release {
  key: string;
  title: string;
  artist_name: string;
  type: ReleaseType;
  status: ReleaseStatus;
  genre: string | null;
  language: string | null;
  description: string | null;
  release_date: string | null;
  cover_url: string | null;
  tracks_count: number;
  created_at: string;
  updated_at: string;
}

export interface Track {
  key: string;
  title: string;
  position: number;
  duration: number | null;
  file_name: string | null;
  file_size: number | null;
  file_format: string | null;
  file_url: string | null;
}

export interface CreateReleasePayload {
  title: string;
  artist_name: string;
  type: ReleaseType;
}

export interface UpdateReleasePayload {
  title?: string;
  artist_name?: string;
  type?: ReleaseType;
  genre?: string | null;
  language?: string | null;
  description?: string | null;
  release_date?: string | null;
}

export interface ReleaseMetrics {
  total: number;
  published: number;
  in_review: number;
  drafts: number;
}
