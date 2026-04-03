import { createFileRoute, Outlet, redirect, Link, useMatchRoute, useNavigate } from '@tanstack/react-router';
import { useAuth } from '@/lib/auth';
import { Button } from '@/components/ui/button';
import {
  LayoutDashboard,
  Disc3,
  Users,
  Wrench,
  ShoppingCart,
  LogOut,
  Music2,
  Menu,
  X,
} from 'lucide-react';
import { useState, useEffect } from 'react';
import { ThemeToggle } from '@/components/theme-toggle';

export const Route = createFileRoute('/_admin')({
  beforeLoad: () => {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      throw redirect({ to: '/login' });
    }
  },
  component: AdminLayout,
});

const sidebarItems = [
  { to: '/admin' as const, label: 'Дашборд', icon: LayoutDashboard, exact: true },
  { to: '/admin/releases' as const, label: 'Релизы', icon: Disc3, exact: false },
  { to: '/admin/artists' as const, label: 'Артисты', icon: Users, exact: false },
  { to: '/admin/services' as const, label: 'Сервисы', icon: Wrench, exact: false },
  { to: '/admin/orders' as const, label: 'Заказы', icon: ShoppingCart, exact: false },
];

function AdminLayout() {
  const { user, logout, isLoading } = useAuth();
  const navigate = useNavigate();
  const matchRoute = useMatchRoute();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  useEffect(() => {
    if (!isLoading && user && user.role === 'artist') {
      void navigate({ to: '/dashboard' });
    }
  }, [user, isLoading, navigate]);

  if (isLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center">
        <div className="h-8 w-8 animate-spin rounded-full border-2 border-primary border-t-transparent" />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Mobile header */}
      <header className="sticky top-0 z-40 flex h-14 items-center justify-between border-b bg-background/80 px-4 backdrop-blur-sm lg:hidden">
        <div className="flex items-center gap-2">
          <button onClick={() => setSidebarOpen(!sidebarOpen)}>
            {sidebarOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
          <Music2 className="h-5 w-5 text-primary" />
          <span className="text-sm font-semibold">Admin</span>
        </div>
        <ThemeToggle />
        <Button variant="ghost" size="sm" onClick={logout}>
          <LogOut className="h-4 w-4" />
        </Button>
      </header>

      <div className="flex">
        {/* Sidebar */}
        <aside
          className={`fixed inset-y-0 left-0 z-30 w-56 transform border-r bg-background transition-transform lg:static lg:translate-x-0 ${
            sidebarOpen ? 'translate-x-0' : '-translate-x-full'
          }`}
        >
          <div className="flex h-14 items-center gap-2 border-b px-4">
            <Music2 className="h-5 w-5 text-primary" />
            <span className="text-sm font-semibold tracking-tight">Future Label</span>
            <span className="ml-auto rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-medium text-primary">
              Admin
            </span>
          </div>
          <nav className="flex flex-col gap-0.5 p-2">
            {sidebarItems.map((item) => {
              const isActive = item.exact
                ? matchRoute({ to: item.to })
                : matchRoute({ to: item.to, fuzzy: true });
              return (
                <Link
                  key={item.to}
                  to={item.to}
                  onClick={() => setSidebarOpen(false)}
                  className={`flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors ${
                    isActive
                      ? 'bg-primary/10 text-primary'
                      : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                  }`}
                >
                  <item.icon className="h-4 w-4" />
                  {item.label}
                </Link>
              );
            })}
          </nav>
          <div className="mt-auto border-t p-3">
            <div className="flex items-center gap-2">
              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                {user?.name?.charAt(0)?.toUpperCase() ?? 'A'}
              </div>
              <div className="flex-1 overflow-hidden">
                <p className="truncate text-sm font-medium">{user?.name}</p>
                <p className="truncate text-xs text-muted-foreground">{user?.email}</p>
              </div>
              <ThemeToggle />
              <Button variant="ghost" size="sm" onClick={logout} className="h-8 w-8 p-0">
                <LogOut className="h-4 w-4" />
              </Button>
            </div>
          </div>
        </aside>

        {/* Backdrop for mobile */}
        {sidebarOpen && (
          <div
            className="fixed inset-0 z-20 bg-black/20 lg:hidden"
            onClick={() => setSidebarOpen(false)}
          />
        )}

        {/* Main content */}
        <main className="flex-1 overflow-auto">
          <div className="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}
