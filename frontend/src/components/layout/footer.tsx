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
    <footer className="border-t bg-secondary/50">
      <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 gap-8 md:grid-cols-4">
          {/* Brand */}
          <div className="col-span-2 md:col-span-1">
            <Link to="/" className="flex items-center gap-2 text-lg font-semibold tracking-tight">
              <span>Future</span>
              <span className="inline-block h-2 w-2 rounded-full bg-primary" />
              <span>Label</span>
            </Link>
            <p className="mt-3 text-sm text-muted-foreground">
              Христианский музыкальный лейбл. Помогаем артистам делиться музыкой со всем миром.
            </p>
          </div>

          {/* Product */}
          <div>
            <h4 className="mb-3 text-sm font-semibold">Продукт</h4>
            <ul className="space-y-2">
              {footerLinks.product.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-sm text-muted-foreground transition-colors hover:text-foreground"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Company */}
          <div>
            <h4 className="mb-3 text-sm font-semibold">Компания</h4>
            <ul className="space-y-2">
              {footerLinks.company.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-sm text-muted-foreground transition-colors hover:text-foreground"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Legal */}
          <div>
            <h4 className="mb-3 text-sm font-semibold">Документы</h4>
            <ul className="space-y-2">
              {footerLinks.legal.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-sm text-muted-foreground transition-colors hover:text-foreground"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        <div className="mt-10 border-t pt-6">
          <p className="text-center text-sm text-muted-foreground">
            &copy; {new Date().getFullYear()} Future Label. Все права защищены.
          </p>
        </div>
      </div>
    </footer>
  )
}
