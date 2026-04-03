import { Link } from '@tanstack/react-router'
import { useState } from 'react'
import { Menu, X } from 'lucide-react'

const navLinks = [
  { label: 'Услуги', href: '/#services', isHash: true },
  { label: 'Тарифы', href: '/#pricing', isHash: true },
  { label: 'О нас', href: '/about', isHash: false },
] as const

function NavLink({
  link,
  className,
  onClick,
}: {
  link: (typeof navLinks)[number]
  className: string
  onClick?: () => void
}) {
  if (link.isHash) {
    return (
      <a href={link.href} className={className} onClick={onClick}>
        {link.label}
      </a>
    )
  }
  return (
    <Link to={link.href} className={className} onClick={onClick}>
      {link.label}
    </Link>
  )
}

export function Header() {
  const [mobileOpen, setMobileOpen] = useState(false)

  return (
    <header className="sticky top-0 z-50 w-full border-b border-black/[0.06] bg-white/80 backdrop-blur-xl">
      <div className="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-6">
        {/* Logo */}
        <Link
          to="/"
          className="flex items-center gap-0.5 text-[16px] font-semibold tracking-[-0.02em] text-[#0f172a]"
        >
          Future
          <span className="mx-[3px] inline-block h-[6px] w-[6px] rounded-full bg-[#2563eb]" />
          Label
        </Link>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-8 md:flex">
          {navLinks.map((link) => (
            <NavLink
              key={link.label}
              link={link}
              className="text-[14px] font-medium text-[#64748b] transition-colors duration-200 hover:text-[#0f172a]"
            />
          ))}
        </nav>

        {/* Desktop CTA */}
        <div className="hidden items-center gap-3 md:flex">
          <Link
            to="/login"
            className="rounded-xl border border-black/[0.06] px-5 py-2 text-[14px] font-medium text-[#0f172a] transition-all duration-200 hover:bg-[#f8f9fa]"
          >
            Войти
          </Link>
          <Link
            to="/register"
            className="rounded-xl bg-[#2563eb] px-5 py-2 text-[14px] font-medium text-white shadow-sm transition-all duration-200 hover:bg-[#1d4ed8] hover:shadow-md"
          >
            Начать
          </Link>
        </div>

        {/* Mobile menu button */}
        <button
          className="inline-flex items-center justify-center rounded-xl p-2 text-[#64748b] transition-colors duration-200 hover:text-[#0f172a] md:hidden"
          onClick={() => setMobileOpen(!mobileOpen)}
          aria-label="Меню"
        >
          {mobileOpen ? <X className="size-5" /> : <Menu className="size-5" />}
        </button>
      </div>

      {/* Mobile nav */}
      {mobileOpen && (
        <div className="border-t border-black/[0.06] px-6 pb-6 pt-2 md:hidden">
          <nav className="flex flex-col gap-1">
            {navLinks.map((link) => (
              <NavLink
                key={link.label}
                link={link}
                className="rounded-xl px-3 py-2.5 text-[14px] font-medium text-[#64748b] transition-colors duration-200 hover:bg-[#f8f9fa] hover:text-[#0f172a]"
                onClick={() => setMobileOpen(false)}
              />
            ))}
          </nav>
          <div className="mt-4 flex flex-col gap-2">
            <Link
              to="/login"
              className="flex items-center justify-center rounded-xl border border-black/[0.06] px-4 py-2.5 text-[14px] font-medium text-[#0f172a] transition-colors duration-200 hover:bg-[#f8f9fa]"
            >
              Войти
            </Link>
            <Link
              to="/register"
              className="flex items-center justify-center rounded-xl bg-[#2563eb] px-4 py-2.5 text-[14px] font-medium text-white transition-all duration-200 hover:bg-[#1d4ed8]"
            >
              Начать
            </Link>
          </div>
        </div>
      )}
    </header>
  )
}
