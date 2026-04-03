import { Link } from '@tanstack/react-router'
import { useState } from 'react'
import { Menu, X } from 'lucide-react'

const navLinks = [
  { label: 'Услуги', href: '/#services', isHash: true },
  { label: 'Тарифы', href: '/pricing', isHash: false },
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
    <header
      className="sticky top-0 z-50 w-full"
      style={{
        backgroundColor: 'rgba(255, 255, 255, 0.92)',
        backdropFilter: 'blur(12px)',
        WebkitBackdropFilter: 'blur(12px)',
        borderBottom: '0.5px solid rgba(229, 229, 227, 0.8)',
      }}
    >
      <div className="mx-auto flex h-[64px] max-w-[1120px] items-center justify-between px-6">
        {/* Logo */}
        <Link to="/" className="flex items-center gap-0.5 text-[17px] font-semibold tracking-[-0.02em]">
          <span className="text-[#1a1a1a]">Future</span>
          <span
            className="mx-[3px] inline-block h-[7px] w-[7px] rounded-full"
            style={{ backgroundColor: '#185FA5' }}
          />
          <span className="text-[#1a1a1a]">Label</span>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-10 md:flex">
          {navLinks.map((link) => (
            <NavLink
              key={link.label}
              link={link}
              className="text-[14px] font-medium text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a]"
            />
          ))}
        </nav>

        {/* Desktop CTA */}
        <div className="hidden items-center gap-3 md:flex">
          <Link
            to="/login"
            className="rounded-lg px-4 py-[7px] text-[14px] font-medium text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a]"
          >
            Войти
          </Link>
          <Link
            to="/register"
            className="rounded-lg px-5 py-[7px] text-[14px] font-medium text-white transition-all duration-200 hover:opacity-90"
            style={{ backgroundColor: '#185FA5' }}
          >
            Начать
          </Link>
        </div>

        {/* Mobile menu button */}
        <button
          className="inline-flex items-center justify-center rounded-lg p-2 text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a] md:hidden"
          onClick={() => setMobileOpen(!mobileOpen)}
          aria-label="Меню"
        >
          {mobileOpen ? <X className="size-5" /> : <Menu className="size-5" />}
        </button>
      </div>

      {/* Mobile nav */}
      {mobileOpen && (
        <div
          className="px-6 pb-6 pt-2 md:hidden"
          style={{ borderTop: '0.5px solid rgba(229, 229, 227, 0.8)' }}
        >
          <nav className="flex flex-col gap-1">
            {navLinks.map((link) => (
              <NavLink
                key={link.label}
                link={link}
                className="rounded-lg px-3 py-2.5 text-[14px] font-medium text-[#6b6b6b] transition-colors duration-200 hover:bg-[#f7f7f5] hover:text-[#1a1a1a]"
                onClick={() => setMobileOpen(false)}
              />
            ))}
          </nav>
          <div className="mt-4 flex flex-col gap-2">
            <Link
              to="/login"
              className="flex items-center justify-center rounded-lg border border-[#e5e5e3] px-4 py-2.5 text-[14px] font-medium text-[#1a1a1a] transition-colors duration-200 hover:bg-[#f7f7f5]"
            >
              Войти
            </Link>
            <Link
              to="/register"
              className="flex items-center justify-center rounded-lg px-4 py-2.5 text-[14px] font-medium text-white transition-all duration-200 hover:opacity-90"
              style={{ backgroundColor: '#185FA5' }}
            >
              Начать
            </Link>
          </div>
        </div>
      )}
    </header>
  )
}
