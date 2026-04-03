export type ContractStatus = 'pending' | 'signed' | 'expired' | 'cancelled';

export interface Contract {
  key: string;
  template_version: string;
  status: ContractStatus;
  pdf_url: string | null;
  accepted_at: string | null;
  accepted_ip: string | null;
  created_at: string;
  updated_at: string;
}
