import { createFileRoute, Link, useNavigate } from '@tanstack/react-router';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useAuth } from '@/lib/auth';
import type { AxiosError } from 'axios';

const loginSearchSchema = z.object({
  registered: z.enum(['true']).optional(),
});

export const Route = createFileRoute('/login')({
  component: LoginPage,
  validateSearch: loginSearchSchema,
});

const loginSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(1, 'Password is required'),
});

type LoginFormData = z.infer<typeof loginSchema>;

interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

function LoginPage() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const { registered } = Route.useSearch();
  const [serverError, setServerError] = useState<string | null>(null);

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data: LoginFormData) => {
    setServerError(null);
    try {
      const user = await login(data.email, data.password);

      if (user.role === 'artist') {
        await navigate({ to: '/dashboard' });
      } else {
        await navigate({ to: '/admin' });
      }
    } catch (err) {
      const axiosError = err as AxiosError<ApiError>;
      setServerError(
        axiosError.response?.data?.message ?? 'Invalid credentials. Please try again.',
      );
    }
  };

  return (
    <AuthLayout
      title="Welcome back"
      description="Sign in to your account"
      footer={
        <span>
          Don't have an account?{' '}
          <Link to="/register" className="font-medium text-foreground underline underline-offset-4 hover:text-accent">
            Sign up
          </Link>
        </span>
      }
    >
      <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
        {registered && (
          <div className="rounded-md border border-accent/50 bg-accent/10 px-3 py-2 text-sm text-accent">
            Registration successful. Please check your email to verify your account, then sign in.
          </div>
        )}

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
          <div className="flex items-center justify-between">
            <Label htmlFor="password">Password</Label>
            <Link
              to="/forgot-password"
              className="text-xs text-muted-foreground underline underline-offset-4 hover:text-foreground"
            >
              Forgot password?
            </Link>
          </div>
          <Input
            id="password"
            type="password"
            placeholder="Enter your password"
            autoComplete="current-password"
            aria-invalid={!!errors.password}
            {...register('password')}
          />
          {errors.password && (
            <p className="text-xs text-destructive">{errors.password.message}</p>
          )}
        </div>

        <Button type="submit" className="mt-2 w-full" disabled={isSubmitting}>
          {isSubmitting ? 'Signing in...' : 'Sign in'}
        </Button>
      </form>
    </AuthLayout>
  );
}
