import { createFileRoute } from '@tanstack/react-router'
import { Pricing } from '@/components/landing/pricing'
import { Faq } from '@/components/landing/faq'

export const Route = createFileRoute('/_public/pricing')({
  component: PricingPage,
  head: () => ({
    meta: [
      { title: 'Тарифы — Future Label' },
      {
        name: 'description',
        content:
          'Прозрачные тарифы на цифровую дистрибуцию музыки. Бесплатный, Стандарт и Премиум планы.',
      },
    ],
  }),
})

function PricingPage() {
  return (
    <>
      <Pricing detailed />

      {/* Comparison table */}
      <section className="px-4 py-20 sm:px-6 sm:py-24">
        <div className="mx-auto max-w-4xl">
          <h2 className="mb-8 text-center text-2xl font-bold tracking-tight sm:text-3xl">
            Сравнение тарифов
          </h2>

          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b">
                  <th className="pb-4 text-left font-medium text-muted-foreground">Функция</th>
                  <th className="pb-4 text-center font-medium">Бесплатно</th>
                  <th className="pb-4 text-center font-medium text-primary">Стандарт</th>
                  <th className="pb-4 text-center font-medium">Премиум</th>
                </tr>
              </thead>
              <tbody className="divide-y">
                {comparisonRows.map((row) => (
                  <tr key={row.feature}>
                    <td className="py-3 pr-4 text-muted-foreground">{row.feature}</td>
                    <td className="py-3 text-center">{row.free}</td>
                    <td className="py-3 text-center">{row.standard}</td>
                    <td className="py-3 text-center">{row.premium}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <Faq />
    </>
  )
}

const comparisonRows = [
  { feature: 'Площадки', free: '50+', standard: '100+', premium: '100+' },
  { feature: 'Роялти артисту', free: '85%', standard: '90%', premium: '95%' },
  { feature: 'ISRC коды', free: 'Да', standard: 'Да', premium: 'Да' },
  { feature: 'UPC коды', free: 'Нет', standard: 'Да', premium: 'Да' },
  { feature: 'Питчинг в плейлисты', free: 'Нет', standard: 'Да', premium: 'Да' },
  { feature: 'Аналитика', free: 'Базовая', standard: 'Расширенная', premium: 'Полная' },
  { feature: 'Промо-кампания', free: 'Нет', standard: 'Нет', premium: 'Да' },
  { feature: 'Персональный менеджер', free: 'Нет', standard: 'Нет', premium: 'Да' },
  { feature: 'Верификация в стримингах', free: 'Нет', standard: 'Нет', premium: 'Да' },
  { feature: 'Canvas для Spotify', free: 'Нет', standard: 'Нет', premium: 'Да' },
  { feature: 'Поддержка', free: 'Email', standard: 'Приоритетная', premium: '24/7' },
]
