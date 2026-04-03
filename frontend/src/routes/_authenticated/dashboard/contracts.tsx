import { createFileRoute } from '@tanstack/react-router';
import { useContracts, useDownloadContract, useAcceptContract } from '@/hooks/use-contracts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { CheckCircle, Download, FileText, Loader2 } from 'lucide-react';
import type { ContractStatus } from '@/types/contract';

export const Route = createFileRoute('/_authenticated/dashboard/contracts')({
  component: ContractsPage,
});

const statusConfig: Record<ContractStatus, { label: string; className: string }> = {
  pending: { label: 'Ожидает', className: 'bg-amber-50 text-amber-700 border-amber-200' },
  signed: { label: 'Подписан', className: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
  expired: { label: 'Истёк', className: 'bg-gray-100 text-gray-700 border-gray-200' },
  cancelled: { label: 'Отменён', className: 'bg-red-50 text-red-700 border-red-200' },
};

function ContractsPage() {
  const { data: contracts = [], isLoading } = useContracts();
  const downloadContract = useDownloadContract();
  const acceptContract = useAcceptContract();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold tracking-tight">Контракты</h1>
        <p className="text-sm text-muted-foreground">
          Управляйте контрактами и скачивайте документы
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Мои контракты</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : contracts.length === 0 ? (
            <div className="flex flex-col items-center py-12">
              <FileText className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm text-muted-foreground">Нет контрактов</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Версия</TableHead>
                    <TableHead>Статус</TableHead>
                    <TableHead className="hidden md:table-cell">Принят</TableHead>
                    <TableHead className="hidden md:table-cell">Создан</TableHead>
                    <TableHead className="w-10"></TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {contracts.map((contract) => {
                    const config = statusConfig[contract.status];
                    return (
                      <TableRow key={contract.key}>
                        <TableCell className="text-sm font-medium">
                          {contract.template_version}
                        </TableCell>
                        <TableCell>
                          <Badge variant="outline" className={config.className}>
                            {config.label}
                          </Badge>
                        </TableCell>
                        <TableCell className="hidden text-sm text-muted-foreground md:table-cell">
                          {contract.accepted_at
                            ? new Date(contract.accepted_at).toLocaleDateString('ru-RU')
                            : '--'}
                        </TableCell>
                        <TableCell className="hidden text-sm text-muted-foreground md:table-cell">
                          {new Date(contract.created_at).toLocaleDateString('ru-RU')}
                        </TableCell>
                        <TableCell>
                          <div className="flex items-center gap-1">
                            {contract.status === 'pending' && (
                              <Button
                                variant="default"
                                size="sm"
                                className="h-8 gap-1 text-xs"
                                disabled={acceptContract.isPending}
                                onClick={() => acceptContract.mutate(contract.key)}
                              >
                                {acceptContract.isPending ? (
                                  <Loader2 className="h-3.5 w-3.5 animate-spin" />
                                ) : (
                                  <CheckCircle className="h-3.5 w-3.5" />
                                )}
                                Подписать
                              </Button>
                            )}
                            {contract.pdf_url && (
                              <Button
                                variant="ghost"
                                size="sm"
                                className="h-8 w-8 p-0"
                                disabled={downloadContract.isPending}
                                onClick={() => downloadContract.mutate(contract.key)}
                              >
                                {downloadContract.isPending ? (
                                  <Loader2 className="h-4 w-4 animate-spin" />
                                ) : (
                                  <Download className="h-4 w-4" />
                                )}
                              </Button>
                            )}
                          </div>
                        </TableCell>
                      </TableRow>
                    );
                  })}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
