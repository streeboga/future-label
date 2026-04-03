import { createFileRoute } from '@tanstack/react-router';
import { useState } from 'react';
import { useServices, useCreateService, useUpdateService, useDeleteService } from '@/hooks/use-services';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import type { Service, CreateServicePayload, UpdateServicePayload } from '@/types/service';
import { Plus, Pencil, Trash2, Loader2, Wrench } from 'lucide-react';

export const Route = createFileRoute('/_admin/admin/services')({
  component: AdminServices,
});

type ModalState =
  | { type: 'closed' }
  | { type: 'create' }
  | { type: 'edit'; service: Service };

function AdminServices() {
  const { data: services = [], isLoading } = useServices();
  const createService = useCreateService();
  const updateService = useUpdateService();
  const deleteService = useDeleteService();

  const [modal, setModal] = useState<ModalState>({ type: 'closed' });
  const [deleteConfirm, setDeleteConfirm] = useState<string | null>(null);

  // Form state
  const [formTitle, setFormTitle] = useState('');
  const [formDescription, setFormDescription] = useState('');
  const [formPrice, setFormPrice] = useState('');
  const [formCategory, setFormCategory] = useState('');
  const [formSortOrder, setFormSortOrder] = useState('0');
  const [formIsActive, setFormIsActive] = useState(true);

  const openCreate = () => {
    setFormTitle('');
    setFormDescription('');
    setFormPrice('');
    setFormCategory('');
    setFormSortOrder('0');
    setFormIsActive(true);
    setModal({ type: 'create' });
  };

  const openEdit = (service: Service) => {
    setFormTitle(service.title);
    setFormDescription(service.description ?? '');
    setFormPrice(String(service.price));
    setFormCategory(service.category);
    setFormSortOrder(String(service.sort_order));
    setFormIsActive(service.is_active);
    setModal({ type: 'edit', service });
  };

  const handleSave = async () => {
    if (modal.type === 'create') {
      const payload: CreateServicePayload = {
        title: formTitle,
        description: formDescription || null,
        price: Number(formPrice),
        category: formCategory,
        sort_order: Number(formSortOrder),
        is_active: formIsActive,
      };
      await createService.mutateAsync(payload);
    } else if (modal.type === 'edit') {
      const payload: UpdateServicePayload = {
        title: formTitle,
        description: formDescription || null,
        price: Number(formPrice),
        category: formCategory,
        sort_order: Number(formSortOrder),
        is_active: formIsActive,
      };
      await updateService.mutateAsync({ key: modal.service.key, payload });
    }
    setModal({ type: 'closed' });
  };

  const handleDelete = async (key: string) => {
    await deleteService.mutateAsync(key);
    setDeleteConfirm(null);
  };

  const isMutating = createService.isPending || updateService.isPending;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold tracking-tight">Каталог сервисов</h1>
          <p className="text-sm text-muted-foreground">Управление услугами для артистов</p>
        </div>
        <Button size="sm" className="gap-1.5" onClick={openCreate}>
          <Plus className="h-4 w-4" />
          Добавить
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-base">Сервисы ({services.length})</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-3">
              {Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="h-12 animate-pulse rounded bg-muted" />
              ))}
            </div>
          ) : services.length === 0 ? (
            <div className="flex flex-col items-center py-12">
              <Wrench className="mb-3 h-10 w-10 text-muted-foreground/40" />
              <p className="text-sm text-muted-foreground">Нет сервисов</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Название</TableHead>
                    <TableHead>Категория</TableHead>
                    <TableHead className="text-right">Цена</TableHead>
                    <TableHead className="text-center">Порядок</TableHead>
                    <TableHead className="text-center">Статус</TableHead>
                    <TableHead className="w-20">Действия</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {services.map((service) => (
                    <TableRow key={service.key}>
                      <TableCell>
                        <div>
                          <p className="text-sm font-medium">{service.title}</p>
                          {service.description && (
                            <p className="text-xs text-muted-foreground line-clamp-1">
                              {service.description}
                            </p>
                          )}
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline" className="text-xs">
                          {service.category}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right text-sm font-medium">
                        {service.price.toLocaleString('ru-RU')} {service.currency}
                      </TableCell>
                      <TableCell className="text-center text-sm text-muted-foreground">
                        {service.sort_order}
                      </TableCell>
                      <TableCell className="text-center">
                        <Badge
                          variant="outline"
                          className={
                            service.is_active
                              ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                              : 'bg-gray-100 text-gray-500 border-gray-200'
                          }
                        >
                          {service.is_active ? 'Активен' : 'Неактивен'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="flex gap-1">
                          <Button
                            variant="ghost"
                            size="sm"
                            className="h-7 w-7 p-0"
                            onClick={() => openEdit(service)}
                          >
                            <Pencil className="h-3.5 w-3.5" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="sm"
                            className="h-7 w-7 p-0 text-red-600 hover:text-red-700"
                            onClick={() => setDeleteConfirm(service.key)}
                          >
                            <Trash2 className="h-3.5 w-3.5" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Create/Edit modal */}
      <Dialog
        open={modal.type !== 'closed'}
        onOpenChange={(open) => !open && setModal({ type: 'closed' })}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {modal.type === 'create' ? 'Добавить сервис' : 'Редактировать сервис'}
            </DialogTitle>
          </DialogHeader>
          <div className="space-y-3">
            <div className="space-y-1.5">
              <Label htmlFor="svc-title">Название</Label>
              <Input
                id="svc-title"
                value={formTitle}
                onChange={(e) => setFormTitle(e.target.value)}
              />
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="svc-desc">Описание</Label>
              <Textarea
                id="svc-desc"
                value={formDescription}
                onChange={(e) => setFormDescription(e.target.value)}
                rows={2}
              />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1.5">
                <Label htmlFor="svc-price">Цена</Label>
                <Input
                  id="svc-price"
                  type="number"
                  value={formPrice}
                  onChange={(e) => setFormPrice(e.target.value)}
                />
              </div>
              <div className="space-y-1.5">
                <Label htmlFor="svc-category">Категория</Label>
                <Input
                  id="svc-category"
                  value={formCategory}
                  onChange={(e) => setFormCategory(e.target.value)}
                />
              </div>
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1.5">
                <Label htmlFor="svc-sort">Порядок</Label>
                <Input
                  id="svc-sort"
                  type="number"
                  value={formSortOrder}
                  onChange={(e) => setFormSortOrder(e.target.value)}
                />
              </div>
              <div className="flex items-end gap-2 pb-0.5">
                <Switch
                  checked={formIsActive}
                  onCheckedChange={setFormIsActive}
                />
                <Label className="text-sm">{formIsActive ? 'Активен' : 'Неактивен'}</Label>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setModal({ type: 'closed' })}>
              Отмена
            </Button>
            <Button
              onClick={() => void handleSave()}
              disabled={isMutating || !formTitle || !formPrice || !formCategory}
            >
              {isMutating && <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />}
              Сохранить
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Delete confirm */}
      <Dialog
        open={deleteConfirm !== null}
        onOpenChange={(open) => !open && setDeleteConfirm(null)}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Удалить сервис?</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">Это действие нельзя отменить.</p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteConfirm(null)}>
              Отмена
            </Button>
            <Button
              variant="destructive"
              onClick={() => deleteConfirm && void handleDelete(deleteConfirm)}
              disabled={deleteService.isPending}
            >
              {deleteService.isPending && <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />}
              Удалить
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
