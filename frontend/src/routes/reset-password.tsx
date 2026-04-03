import { createFileRoute, Link, useNavigate } from '@tanstack/react-router';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { resetPassword } from '@/services/auth';
import type { AxiosError } from 'axios';

export const Route = createFileRoute('/reset-password')({
  component: ResetPasswordPage,
  validateSearch: (search: Record<string, unknown>) => ({
    token: (search.token as string) ?? '',
    email: (search.email as string) ?? '',
  }),
});

const resetSchema = z
  .object({
    email: z.string().email('Некорректный email'),
    password: z.string().min(8, 'Пароль минимум 8 символов'),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Пароли не совпадают',
    path: ['password_confirmation'],
  });

type ResetFormData = z.infer<typeof resetSchema>;

interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

function ResetPasswordPage() {
  const navigate = useNavigate();
  const { token, email } = Route.useSearch();
  const [serverError, setServerError] = useState<string | null>(null);

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<ResetFormData>({
    resolver: zodResolver(resetSchema),
    defaultValues: { email },
  });

  const onSubmit = async (data: ResetFormData) => {
    setServerError(null);
    try {
      await resetPassword({ ...data, token });
      await navigate({ to: '/login' });
    } catch (err) {
      const axiosError = err as AxiosError<ApiError>;
      const responseErrors = axiosError.response?.data?.errors;
      if (responseErrors) {
        for (const [field, messages] of Object.entries(responseErrors)) {
          if (['email', 'password', 'password_confirmation'].includes(field)) {
            setError(field as keyof ResetFormData, { message: messages[0] });
          }
        }
      } else {
        setServerError(
          axiosError.response?.data?.message ?? 'Не удалось сбросить пароль. Ссылка могла истечь.',
        );
      }
    }
  };

  if (!token) {
    return (
      <AuthLayout title="Недействительная ссылка" description="Эта ссылка для сброса пароля недействительна или истекла.">
        <div className="text-center">
          <Link to="/forgot-password" className="font-medium text-foreground underline underline-offset-4 hover:text-accent">
            Запросить новую ссылку
          </Link>
        </div>
      </AuthLayout>
    );
  }

  return (
    <AuthLayout
      title="Новый пароль"
      description="Введите новый пароль"
      footer={
        <Link to="/login" className="font-medium text-foreground underline underline-offset-4 hover:text-accent">
          Назад к входу
        </Link>
      }
    >
      <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
        {serverError && (
          <div className="rounded-md border border-destructive/50 bg-destructive/10 px-3 py-2 text-sm text-destructive">
            {serverError}
          </div>
        )}

        <div className="flex flex-col gap-2">
          <Label htmlFor="email">Email</Label>
          <Input
            id="email"
            type="email"
            placeholder="you@example.com"
            autoComplete="email"
            aria-invalid={!!errors.email}
            {...register('email')}
          />
          {errors.email && (
            <p className="text-xs text-destructive">{errors.email.message}</p>
          )}
        </div>

        <div className="flex flex-col gap-2">
          <Label htmlFor="password">Новый пароль</Label>
          <Input
            id="password"
            type="password"
            placeholder="Мин. 8 символов"
            autoComplete="new-password"
            aria-invalid={!!errors.password}
            {...register('password')}
          />
          {errors.password && (
            <p className="text-xs text-destructive">{errors.password.message}</p>
          )}
        </div>

        <div className="flex flex-col gap-2">
          <Label htmlFor="password_confirmation">Подтвердите пароль</Label>
          <Input
            id="password_confirmation"
            type="password"
            placeholder="Повторите пароль"
            autoComplete="new-password"
            aria-invalid={!!errors.password_confirmation}
            {...register('password_confirmation')}
          />
          {errors.password_confirmation && (
            <p className="text-xs text-destructive">{errors.password_confirmation.message}</p>
          )}
        </div>

        <Button type="submit" className="mt-2 w-full" disabled={isSubmitting}>
          {isSubmitting ? 'Сброс...' : 'Сбросить пароль'}
        </Button>
      </form>
    </AuthLayout>
  );
}
