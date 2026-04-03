export type ReleaseType = 'single' | 'ep' | 'album';

export type ReleaseStatus = 'draft' | 'awaiting_payment' | 'awaiting_contract' | 'in_review' | 'approved' | 'published' | 'rejected';

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
  reject_reason: string | null;
  created_at: string;
  updated_at: string;
}

export interface Track {
  key: string;
  title: string;
  track_number: number;
  duration_seconds: number | null;
  file_url: string | null;
  format: string;
  file_size: number | null;
  authors: string | null;
  composers: string | null;
  lyrics: string | null;
  isrc: string | null;
  created_at: string;
  updated_at: string;
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
  cover_url?: string | null;
  metadata?: Record<string, unknown> | null;
}
