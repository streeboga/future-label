import { createFileRoute, Outlet, redirect } from '@tanstack/react-router';

export const Route = createFileRoute('/_admin')({
  beforeLoad: () => {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      throw redirect({ to: '/login' });
    }
    // Role check will happen at component level since we need the user context
  },
  component: AdminLayout,
});

function AdminLayout() {
  return <Outlet />;
}
