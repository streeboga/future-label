import { createFileRoute, Link, useNavigate } from '@tanstack/react-router';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register as registerUser } from '@/services/auth';
import type { AxiosError } from 'axios';

export const Route = createFileRoute('/register')({
  component: RegisterPage,
});

const registerSchema = z
  .object({
    name: z.string().min(1, 'Введите имя'),
    email: z.string().email('Некорректный email'),
    password: z.string().min(8, 'Пароль минимум 8 символов'),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Пароли не совпадают',
    path: ['password_confirmation'],
  });

type RegisterFormData = z.infer<typeof registerSchema>;

interface ApiValidationError {
  message: string;
  errors?: Record<string, string[]>;
}

function RegisterPage() {
  const navigate = useNavigate();
  const [serverError, setServerError] = useState<string | null>(null);

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<RegisterFormData>({
    resolver: zodResolver(registerSchema),
  });

  const onSubmit = async (data: RegisterFormData) => {
    setServerError(null);
    try {
      await registerUser(data);
      await navigate({
        to: '/login',
        search: { registered: 'true' },
      });
    } catch (err) {
      const axiosError = err as AxiosError<ApiValidationError>;
      const responseErrors = axiosError.response?.data?.errors;
      if (responseErrors) {
        for (const [field, messages] of Object.entries(responseErrors)) {
          if (field in registerSchema.shape || field === 'password_confirmation') {
            setError(field as keyof RegisterFormData, {
              message: messages[0],
            });
          }
        }
      } else {
        setServerError(
          axiosError.response?.data?.message ?? 'Ошибка регистрации. Попробуйте ещё раз.',
        );
      }
    }
  };

  return (
    <AuthLayout
      title="Создать аккаунт"
      description="Заполните данные для регистрации"
      footer={
        <span>
          Уже есть аккаунт?{' '}
          <Link to="/login" className="font-medium text-foreground underline underline-offset-4 hover:text-accent">
            Войти
          </Link>
        </span>
      }
    >
      <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
        {serverError && (
          <div className="rounded-md border border-destructive/50 bg-destructive/10 px-3 py-2 text-sm text-destructive">
            {serverError}
          </div>
        )}

        <div className="flex flex-col gap-2">
          <Label htmlFor="name">Имя</Label>
          <Input
            id="name"
            placeholder="Иван Иванов"
            autoComplete="name"
            aria-invalid={!!errors.name}
            {...register('name')}
          />
          {errors.name && (
            <p className="text-xs text-destructive">{errors.name.message}</p>
          )}
        </div>

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
          <Label htmlFor="password">Пароль</Label>
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
          {isSubmitting ? 'Создание...' : 'Создать аккаунт'}
        </Button>
      </form>
    </AuthLayout>
  );
}
