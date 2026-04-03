import { createFileRoute, Outlet, redirect, Link, useMatchRoute } from '@tanstack/react-router';
import { useAuth } from '@/lib/auth';
import { Button } from '@/components/ui/button';
import {
  Bell,
  LayoutDashboard,
  Music2,
  FileText,
  LogOut,
  Menu,
  X,
} from 'lucide-react';
import { useState } from 'react';
import { ThemeToggle } from '@/components/theme-toggle';

export const Route = createFileRoute('/_authenticated')({
  beforeLoad: () => {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      throw redirect({ to: '/login' });
    }
  },
  component: AuthenticatedLayout,
});

const navItems = [
  { to: '/dashboard' as const, label: 'Дашборд', icon: LayoutDashboard },
  { to: '/dashboard/contracts' as const, label: 'Контракты', icon: FileText },
  { to: '/dashboard/notifications' as const, label: 'Уведомления', icon: Bell },
];

function AuthenticatedLayout() {
  const { user, logout } = useAuth();
  const [mobileOpen, setMobileOpen] = useState(false);
  const matchRoute = useMatchRoute();

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="sticky top-0 z-40 border-b bg-background/80 backdrop-blur-sm">
        <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
          <div className="flex items-center gap-6">
            <Link to="/dashboard" className="flex items-center gap-2">
              <Music2 className="h-5 w-5 text-primary" />
              <span className="text-sm font-semibold tracking-tight">Future Label</span>
            </Link>
            <nav className="hidden items-center gap-1 md:flex">
              {navItems.map((item) => {
                const isActive = matchRoute({ to: item.to, fuzzy: true });
                return (
                  <Link
                    key={item.to}
                    to={item.to}
                    className={`flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
                      isActive
                        ? 'bg-primary/10 text-primary'
                        : 'text-muted-foreground hover:text-foreground'
                    }`}
                  >
                    <item.icon className="h-4 w-4" />
                    {item.label}
                  </Link>
                );
              })}
            </nav>
          </div>
          <div className="flex items-center gap-3">
            <span className="hidden text-sm text-muted-foreground sm:inline">
              {user?.name}
            </span>
            <ThemeToggle />
            <Button variant="ghost" size="sm" onClick={logout} className="gap-1.5">
              <LogOut className="h-4 w-4" />
              <span className="hidden sm:inline">Выход</span>
            </Button>
            <button
              className="md:hidden"
              onClick={() => setMobileOpen(!mobileOpen)}
            >
              {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </button>
          </div>
        </div>
        {/* Mobile nav */}
        {mobileOpen && (
          <nav className="border-t px-4 py-2 md:hidden">
            {navItems.map((item) => {
              const isActive = matchRoute({ to: item.to, fuzzy: true });
              return (
                <Link
                  key={item.to}
                  to={item.to}
                  onClick={() => setMobileOpen(false)}
                  className={`flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium ${
                    isActive
                      ? 'bg-primary/10 text-primary'
                      : 'text-muted-foreground'
                  }`}
                >
                  <item.icon className="h-4 w-4" />
                  {item.label}
                </Link>
              );
            })}
          </nav>
        )}
      </header>

      <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <Outlet />
      </main>
    </div>
  );
}
