export interface Service {
  key: string;
  title: string;
  description: string | null;
  price: number;
  currency: string;
  category: string;
  sort_order: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface CreateServicePayload {
  title: string;
  description?: string | null;
  price: number;
  currency?: string;
  category: string;
  sort_order?: number;
  is_active?: boolean;
}

export interface UpdateServicePayload {
  title?: string;
  description?: string | null;
  price?: number;
  currency?: string;
  category?: string;
  sort_order?: number;
  is_active?: boolean;
}
