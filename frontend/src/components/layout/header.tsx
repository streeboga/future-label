import { Link } from '@tanstack/react-router'
import { useState } from 'react'
import { Menu, X } from 'lucide-react'
import { Button } from '@/components/ui/button'

const navLinks = [
  { label: 'Дистрибуция', href: '/' },
  { label: 'Тарифы', href: '/pricing' },
  { label: 'О нас', href: '/about' },
] as const

export function Header() {
  const [mobileOpen, setMobileOpen] = useState(false)

  return (
    <header className="sticky top-0 z-50 w-full border-b border-border/60 bg-white/80 backdrop-blur-lg">
      <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {/* Logo */}
        <Link to="/" className="flex items-center gap-2 text-lg font-semibold tracking-tight">
          <span className="text-foreground">Future</span>
          <span className="inline-block h-2 w-2 rounded-full bg-primary" />
          <span className="text-foreground">Label</span>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-8 md:flex">
          {navLinks.map((link) => (
            <Link
              key={link.label}
              to={link.href}
              className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* Desktop CTA */}
        <div className="hidden items-center gap-3 md:flex">
          <Button variant="ghost" size="sm" asChild>
            <Link to="/login">Войти</Link>
          </Button>
          <Button size="sm" asChild>
            <Link to="/register">Начать</Link>
          </Button>
        </div>

        {/* Mobile menu button */}
        <button
          className="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground transition-colors hover:text-foreground md:hidden"
          onClick={() => setMobileOpen(!mobileOpen)}
          aria-label="Меню"
        >
          {mobileOpen ? <X className="size-5" /> : <Menu className="size-5" />}
        </button>
      </div>

      {/* Mobile nav */}
      {mobileOpen && (
        <div className="border-t bg-white px-4 py-4 md:hidden">
          <nav className="flex flex-col gap-3">
            {navLinks.map((link) => (
              <Link
                key={link.label}
                to={link.href}
                className="rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                onClick={() => setMobileOpen(false)}
              >
                {link.label}
              </Link>
            ))}
          </nav>
          <div className="mt-4 flex flex-col gap-2">
            <Button variant="outline" size="sm" className="w-full" asChild>
              <Link to="/login">Войти</Link>
            </Button>
            <Button size="sm" className="w-full" asChild>
              <Link to="/register">Начать</Link>
            </Button>
          </div>
        </div>
      )}
    </header>
  )
}
