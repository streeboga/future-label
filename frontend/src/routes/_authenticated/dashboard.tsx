import { createFileRoute } from '@tanstack/react-router';
import { useAuth } from '@/lib/auth';
import { Button } from '@/components/ui/button';

export const Route = createFileRoute('/_authenticated/dashboard')({
  component: DashboardPage,
});

function DashboardPage() {
  const { user, logout } = useAuth();

  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-background px-4">
      <h1 className="mb-2 text-2xl font-bold text-foreground">Artist Dashboard</h1>
      <p className="mb-6 text-muted-foreground">
        Welcome, {user?.name ?? 'Artist'}
      </p>
      <Button variant="outline" onClick={logout}>
        Sign out
      </Button>
    </div>
  );
}
