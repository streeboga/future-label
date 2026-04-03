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
    <footer style={{ borderTop: '0.5px solid #e5e5e3', backgroundColor: '#fafaf9' }}>
      <div className="mx-auto max-w-[1120px] px-6 pb-10 pt-16">
        <div className="grid grid-cols-2 gap-10 md:grid-cols-4">
          {/* Brand */}
          <div className="col-span-2 md:col-span-1">
            <Link to="/" className="flex items-center gap-0.5 text-[17px] font-semibold tracking-[-0.02em]">
              <span className="text-[#1a1a1a]">Future</span>
              <span
                className="mx-[3px] inline-block h-[7px] w-[7px] rounded-full"
                style={{ backgroundColor: '#185FA5' }}
              />
              <span className="text-[#1a1a1a]">Label</span>
            </Link>
            <p className="mt-4 max-w-[240px] text-[13px] leading-[1.6] text-[#999]">
              Христианский музыкальный лейбл. Помогаем артистам делиться музыкой со всем миром.
            </p>
          </div>

          {/* Product */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#999]">
              Продукт
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.product.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Company */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#999]">
              Компания
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.company.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Legal */}
          <div>
            <h4 className="mb-4 text-[12px] font-semibold uppercase tracking-[0.06em] text-[#999]">
              Документы
            </h4>
            <ul className="space-y-2.5">
              {footerLinks.legal.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-[14px] text-[#6b6b6b] transition-colors duration-200 hover:text-[#1a1a1a]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        <div className="mt-14" style={{ borderTop: '0.5px solid #e5e5e3' }}>
          <p className="pt-6 text-center text-[13px] text-[#999]">
            &copy; {new Date().getFullYear()} Future Label. Все права защищены.
          </p>
        </div>
      </div>
    </footer>
  )
}
