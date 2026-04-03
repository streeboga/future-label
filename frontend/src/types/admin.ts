export interface Artist {
  key: string;
  name: string;
  email: string;
  role: string;
  stage_name: string | null;
  created_at: string;
}

export interface AdminMetrics {
  total_artists: number;
  releases_this_month: number;
  total_revenue: string;
  pending_moderation: number;
}

export interface ModerationAction {
  action: 'approve' | 'reject' | 'publish';
  comment?: string;
}

export interface Order {
  key: string;
  status: 'pending' | 'paid' | 'in_progress' | 'completed' | 'cancelled';
  notes: string | null;
  created_at: string;
}
