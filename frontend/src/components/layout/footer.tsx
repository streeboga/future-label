import { Link } from '@tanstack/react-router'

const footerLinks = {
  product: [
    { label: 'Дистрибуция', href: '/' },
    { label: 'Питчинг', href: '/' },
    { label: 'Промо', href: '/' },
    { label: 'Тарифы', href: '/pricing' },
  ],
  company: [
    { label: 'О нас', href: '/about' },
    { label: 'Контакты', href: '/' },
    { label: 'Блог', href: '/' },
  ],
  legal: [
    { label: 'Политика конфиденциальности', href: '/' },
    { label: 'Условия использования', href: '/' },
    { label: 'Оферта', href: '/' },
  ],
}

export function Footer() {
  return (
    <footer className="border-t border-black/[0.06] bg-[#f8f9fa]">
      <div className="mx-auto max-w-[1200px] px-6 pb-10 pt-16">
        <div className="grid grid-cols-2 gap-10 md:grid-cols-4">
          {/* Brand */}
          <div className="col-span-2 md:col-span-1">
            <Link
              to="/"
              className="flex items-center gap-0.5 text-[16px] font-semibold tracking-[-0.02em] text-[#0f172a]"
            >
              Future
              <span className="mx-[3px] inline-block h-[6px] w-[6px] rounded-full bg-[#2563eb]" />
              Label
            </Link>
            <p className="mt-4 max-w-[240px] text-[13px] leading-[1.7] text-[#94a3b8]">
              Христианский музыкальный лейбл. Помогаем артистам делиться музыкой со
              всем миром.
            </p>
          </div>

          {/* Product */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#94a3b8]">
              Продукт
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.product.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#64748b] transition-colors duration-200 hover:text-[#0f172a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Company */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#94a3b8]">
              Компания
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.company.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#64748b] transition-colors duration-200 hover:text-[#0f172a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Legal */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#94a3b8]">
              Документы
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.legal.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#64748b] transition-colors duration-200 hover:text-[#0f172a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        <div className="mt-14 border-t border-black/[0.06]">
          <p className="pt-6 text-center text-[13px] text-[#94a3b8]">
            &copy; {new Date().getFullYear()} Future Label. Все права защищены.
          </p>
        </div>
      </div>
    </footer>
  )
}
