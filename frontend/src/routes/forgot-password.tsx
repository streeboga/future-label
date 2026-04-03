import { createFileRoute, Link } from '@tanstack/react-router';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { forgotPassword } from '@/services/auth';

export const Route = createFileRoute('/forgot-password')({
  component: ForgotPasswordPage,
});

const forgotSchema = z.object({
  email: z.string().email('Некорректный email'),
});

type ForgotFormData = z.infer<typeof forgotSchema>;

function ForgotPasswordPage() {
  const [submitted, setSubmitted] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<ForgotFormData>({
    resolver: zodResolver(forgotSchema),
  });

  const onSubmit = async (data: ForgotFormData) => {
    try {
      await forgotPassword(data);
    } catch {
      // Show success regardless to prevent email enumeration
    }
    setSubmitted(true);
  };

  return (
    <AuthLayout
      title="Восстановление пароля"
      description="Введите email для получения ссылки на сброс пароля"
      footer={
        <Link to="/login" className="font-medium text-foreground underline underline-offset-4 hover:text-accent">
          Назад к входу
        </Link>
      }
    >
      {submitted ? (
        <div className="rounded-md border border-accent/50 bg-accent/10 px-3 py-3 text-center text-sm text-accent">
          Если аккаунт с таким email существует, мы отправили ссылку для сброса пароля. Проверьте почту.
        </div>
      ) : (
        <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
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

          <Button type="submit" className="mt-2 w-full" disabled={isSubmitting}>
            {isSubmitting ? 'Отправка...' : 'Отправить ссылку'}
          </Button>
        </form>
      )}
    </AuthLayout>
  );
}
