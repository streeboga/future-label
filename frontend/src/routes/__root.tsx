import { createRootRoute, Outlet, ScrollRestoration } from '@tanstack/react-router'
import { AuthProvider } from '@/lib/auth'

export const Route = createRootRoute({
  component: RootLayout,
})

function RootLayout() {
  return (
    <AuthProvider>
      <Outlet />
      <ScrollRestoration />
    </AuthProvider>
  )
}
