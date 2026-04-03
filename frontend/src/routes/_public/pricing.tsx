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
      <section className="bg-[#f8f9fa] px-6 py-16 md:py-[120px]">
        <div className="mx-auto max-w-4xl">
          <h2 className="mb-12 text-center text-[28px] font-semibold tracking-[-0.02em] text-[#0f172a] md:text-[40px]">
            Сравнение тарифов
          </h2>

          <div className="overflow-x-auto rounded-2xl bg-white p-6 shadow-sm">
            <table className="w-full text-[14px]">
              <thead>
                <tr className="border-b border-black/[0.06]">
                  <th className="pb-4 text-left font-medium text-[#94a3b8]">Функция</th>
                  <th className="pb-4 text-center font-medium text-[#0f172a]">Бесплатно</th>
                  <th className="pb-4 text-center font-medium text-[#2563eb]">Стандарт</th>
                  <th className="pb-4 text-center font-medium text-[#0f172a]">Премиум</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-black/[0.06]">
                {comparisonRows.map((row) => (
                  <tr key={row.feature}>
                    <td className="py-3 pr-4 text-[#64748b]">{row.feature}</td>
                    <td className="py-3 text-center text-[#0f172a]">{row.free}</td>
                    <td className="py-3 text-center text-[#0f172a]">{row.standard}</td>
                    <td className="py-3 text-center text-[#0f172a]">{row.premium}</td>
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
