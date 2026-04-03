import { createFileRoute, useNavigate } from '@tanstack/react-router';
import { useAuth } from '@/lib/auth';
import { Button } from '@/components/ui/button';
import { useEffect } from 'react';

export const Route = createFileRoute('/_admin/admin')({
  component: AdminDashboard,
});

function AdminDashboard() {
  const { user, logout, isLoading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!isLoading && user && user.role === 'artist') {
      void navigate({ to: '/dashboard' });
    }
  }, [user, isLoading, navigate]);

  if (isLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-background">
        <p className="text-muted-foreground">Loading...</p>
      </div>
    );
  }

  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-background px-4">
      <h1 className="mb-2 text-2xl font-bold text-foreground">Admin Dashboard</h1>
      <p className="mb-6 text-muted-foreground">
        Welcome, {user?.name ?? 'Admin'}
      </p>
      <Button variant="outline" onClick={logout}>
        Sign out
      </Button>
    </div>
  );
}
