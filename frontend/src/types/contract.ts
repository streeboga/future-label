export type ContractStatus = 'pending' | 'signed' | 'expired' | 'cancelled';

export interface Contract {
  key: string;
  title: string;
  status: ContractStatus;
  release_key: string | null;
  release_title: string | null;
  signed_at: string | null;
  expires_at: string | null;
  pdf_url: string | null;
  created_at: string;
  updated_at: string;
}
