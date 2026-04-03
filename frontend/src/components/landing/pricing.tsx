import { Check } from 'lucide-react'
import { Link } from '@tanstack/react-router'
import { cn } from '@/lib/utils'

export interface PricingPlan {
  name: string
  price: string
  period: string
  description: string
  features: string[]
  featured?: boolean
  cta: string
}

export const pricingPlans: PricingPlan[] = [
  {
    name: 'Сингл',
    price: '1 000\u20BD',
    period: 'за трек',
    description: 'Для тех, кто начинает свой путь в музыке',
    features: [
      'Дистрибуция на 100+ площадок',
      '85% роялти артисту',
      'Базовая статистика',
      'ISRC коды',
      'Поддержка по email',
    ],
    cta: 'Выбрать',
  },
  {
    name: 'EP / Альбом',
    price: '2 500\u20BD',
    period: 'за релиз',
    description: 'Для активных артистов и продюсеров',
    features: [
      'Все из тарифа Сингл',
      '90% роялти артисту',
      'Расширенная аналитика',
      'Питчинг в плейлисты',
      'ISRC + UPC коды',
      'Предзаказ и прелиз',
      'Приоритетная поддержка',
    ],
    featured: true,
    cta: 'Выбрать',
  },
  {
    name: 'Лейбл 360',
    price: 'По запросу',
    period: 'индивидуально',
    description: 'Полный цикл для профессионалов',
    features: [
      'Все из EP / Альбом',
      '95% роялти артисту',
      'Персональный менеджер',
      'Промо-кампания',
      'Стратегия продвижения',
      'Верификация в стримингах',
      'Концерт-менеджмент',
      'Canvas для Spotify',
    ],
    cta: 'Связаться',
  },
]

interface PricingProps {
  detailed?: boolean
}

export function Pricing({ detailed = false }: PricingProps) {
  return (
    <section id="pricing" className="px-6 py-20 md:py-[100px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <p className="mb-3 text-[13px] font-semibold uppercase tracking-[0.15em] text-[#7c3aed]">
            Тарифы
          </p>
          <h2 className="text-[28px] font-bold uppercase tracking-[-0.01em] text-[#0f0f0f] md:text-[42px]">
            {detailed ? 'Выберите свой тариф' : 'Простые и прозрачные цены'}
          </h2>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {pricingPlans.map((plan) => (
            <div
              key={plan.name}
              className={cn(
                'relative flex flex-col rounded-2xl p-8 transition-all duration-300 hover:shadow-lg',
                plan.featured
                  ? 'bg-gradient-to-br from-violet-600 via-blue-600 to-purple-600 text-white shadow-xl md:scale-[1.04]'
                  : 'bg-[#f5f5f5] hover:-translate-y-1'
              )}
            >
              {plan.featured && (
                <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-white px-5 py-1 text-[12px] font-bold uppercase tracking-[0.1em] text-[#7c3aed] shadow-md">
                  Популярный
                </div>
              )}

              <div className="mb-6">
                <h3 className={cn(
                  'text-[16px] font-bold uppercase tracking-[0.02em]',
                  plan.featured ? 'text-white' : 'text-[#0f0f0f]'
                )}>
                  {plan.name}
                </h3>
                <p className={cn(
                  'mt-1 text-[14px]',
                  plan.featured ? 'text-white/70' : 'text-[#6b7280]'
                )}>
                  {plan.description}
                </p>
              </div>

              <div className="mb-8">
                <span className={cn(
                  'text-[40px] font-extrabold tracking-[-0.02em]',
                  plan.featured ? 'text-white' : 'text-[#0f0f0f]'
                )}>
                  {plan.price}
                </span>
                <span className={cn(
                  'ml-2 text-[14px]',
                  plan.featured ? 'text-white/60' : 'text-[#6b7280]'
                )}>
                  / {plan.period}
                </span>
              </div>

              <ul className="mb-8 flex-1 space-y-3">
                {plan.features.map((feature) => (
                  <li key={feature} className="flex items-start gap-3">
                    <Check className={cn(
                      'mt-0.5 size-4 shrink-0',
                      plan.featured ? 'text-white/80' : 'text-[#7c3aed]'
                    )} />
                    <span className={cn(
                      'text-[14px] leading-[1.5]',
                      plan.featured ? 'text-white/80' : 'text-[#6b7280]'
                    )}>
                      {feature}
                    </span>
                  </li>
                ))}
              </ul>

              <Link
                to="/register"
                className={cn(
                  'flex items-center justify-center rounded-full px-6 py-3.5 text-[14px] font-bold uppercase tracking-[0.04em] transition-all duration-300',
                  plan.featured
                    ? 'bg-white text-[#7c3aed] shadow-md hover:-translate-y-0.5 hover:shadow-lg'
                    : 'bg-[#0f0f0f] text-white hover:-translate-y-0.5 hover:bg-[#1a1a1a] hover:shadow-md'
                )}
              >
                {plan.cta}
              </Link>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
