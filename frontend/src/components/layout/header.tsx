import { Link } from '@tanstack/react-router'
import { useState, useEffect } from 'react'
import { Menu, X } from 'lucide-react'

const navLinks = [
  { label: 'УСЛУГИ', href: '/#services', isHash: true },
  { label: 'ТАРИФЫ', href: '/#pricing', isHash: true },
  { label: 'ВОПРОСЫ', href: '/#faq', isHash: true },
  { label: 'О НАС', href: '/about', isHash: false },
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
  const [scrolled, setScrolled] = useState(false)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 60)
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  return (
    <header
      className={`fixed top-0 z-50 w-full transition-all duration-300 ${
        scrolled
          ? 'bg-white/95 shadow-sm backdrop-blur-xl'
          : 'bg-transparent'
      }`}
    >
      <div className="mx-auto flex h-[72px] max-w-[1200px] items-center justify-between px-6">
        {/* Logo */}
        <Link
          to="/"
          className={`text-[18px] font-extrabold uppercase tracking-[0.06em] transition-colors duration-300 ${
            scrolled ? 'text-[#0f0f0f]' : 'text-white'
          }`}
        >
          Future Label
        </Link>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-8 md:flex">
          {navLinks.map((link) => (
            <NavLink
              key={link.label}
              link={link}
              className={`text-[13px] font-medium tracking-[0.06em] transition-colors duration-300 ${
                scrolled
                  ? 'text-[#6b7280] hover:text-[#0f0f0f]'
                  : 'text-white/70 hover:text-white'
              }`}
            />
          ))}
        </nav>

        {/* Desktop CTA */}
        <div className="hidden items-center gap-3 md:flex">
          <Link
            to="/login"
            className={`text-[13px] font-medium tracking-[0.06em] transition-colors duration-300 ${
              scrolled
                ? 'text-[#6b7280] hover:text-[#0f0f0f]'
                : 'text-white/70 hover:text-white'
            }`}
          >
            ВОЙТИ
          </Link>
          <Link
            to="/register"
            className={`rounded-full border px-6 py-2.5 text-[13px] font-semibold tracking-[0.04em] transition-all duration-300 ${
              scrolled
                ? 'border-[#7c3aed] text-[#7c3aed] hover:bg-[#7c3aed] hover:text-white'
                : 'border-white/40 text-white hover:border-white hover:bg-white/10'
            }`}
          >
            ДИСТРИБУЦИЯ ТРЕКА
          </Link>
        </div>

        {/* Mobile menu button */}
        <button
          className={`inline-flex items-center justify-center rounded-xl p-2 transition-colors duration-300 md:hidden ${
            scrolled ? 'text-[#0f0f0f]' : 'text-white'
          }`}
          onClick={() => setMobileOpen(!mobileOpen)}
          aria-label="Меню"
        >
          {mobileOpen ? <X className="size-6" /> : <Menu className="size-6" />}
        </button>
      </div>

      {/* Mobile nav */}
      {mobileOpen && (
        <div className={`px-6 pb-6 pt-2 md:hidden ${scrolled ? 'bg-white' : 'bg-[#0f0f0f]/90 backdrop-blur-xl'}`}>
          <nav className="flex flex-col gap-1">
            {navLinks.map((link) => (
              <NavLink
                key={link.label}
                link={link}
                className={`rounded-xl px-3 py-3 text-[13px] font-medium tracking-[0.06em] transition-colors duration-200 ${
                  scrolled
                    ? 'text-[#6b7280] hover:bg-[#f5f5f5] hover:text-[#0f0f0f]'
                    : 'text-white/70 hover:text-white'
                }`}
                onClick={() => setMobileOpen(false)}
              />
            ))}
          </nav>
          <div className="mt-4 flex flex-col gap-2">
            <Link
              to="/login"
              className={`flex items-center justify-center rounded-full border px-4 py-3 text-[13px] font-medium tracking-[0.06em] transition-colors duration-200 ${
                scrolled
                  ? 'border-[#e5e5e5] text-[#0f0f0f] hover:bg-[#f5f5f5]'
                  : 'border-white/30 text-white hover:bg-white/10'
              }`}
            >
              ВОЙТИ
            </Link>
            <Link
              to="/register"
              className="flex items-center justify-center rounded-full bg-[#7c3aed] px-4 py-3 text-[13px] font-semibold tracking-[0.04em] text-white transition-all duration-200 hover:bg-[#6d28d9]"
            >
              ДИСТРИБУЦИЯ ТРЕКА
            </Link>
          </div>
        </div>
      )}
    </header>
  )
}
