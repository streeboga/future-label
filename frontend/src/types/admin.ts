export interface Artist {
  key: string;
  name: string;
  email: string;
  releases_count: number;
  created_at: string;
}

export interface AdminMetrics {
  total_artists: number;
  releases_this_month: number;
  revenue: number;
  pending_moderation: number;
}

export interface ModerationAction {
  action: 'approve' | 'reject';
  comment?: string;
}

export interface Order {
  key: string;
  release_title: string;
  artist_name: string;
  service_title: string;
  amount: number;
  currency: string;
  status: 'pending' | 'paid' | 'cancelled' | 'refunded';
  created_at: string;
}
