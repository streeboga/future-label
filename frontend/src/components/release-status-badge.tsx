import { Badge } from '@/components/ui/badge';
import type { ReleaseStatus } from '@/types/release';

const statusConfig: Record<ReleaseStatus, { label: string; className: string }> = {
  draft: {
    label: 'Черновик',
    className: 'bg-gray-100 text-gray-700 border-gray-200',
  },
  in_review: {
    label: 'На проверке',
    className: 'bg-amber-50 text-amber-700 border-amber-200',
  },
  approved: {
    label: 'Одобрен',
    className: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  },
  published: {
    label: 'Опубликован',
    className: 'bg-blue-50 text-blue-700 border-blue-200',
  },
  rejected: {
    label: 'Отклонён',
    className: 'bg-red-50 text-red-700 border-red-200',
  },
};

export function ReleaseStatusBadge({ status }: { status: ReleaseStatus }) {
  const config = statusConfig[status];
  return (
    <Badge variant="outline" className={config.className}>
      {config.label}
    </Badge>
  );
}
