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
    name: 'Бесплатно',
    price: '0\u20BD',
    period: 'за сингл',
    description: 'Для тех, кто начинает свой путь в музыке',
    features: [
      'Дистрибуция на 50+ площадок',
      '85% роялти артисту',
      'Базовая статистика',
      'Поддержка по email',
      'ISRC коды',
    ],
    cta: 'Начать бесплатно',
  },
  {
    name: 'Стандарт',
    price: '1 500\u20BD',
    period: 'за релиз',
    description: 'Для активных артистов и продюсеров',
    features: [
      'Дистрибуция на 100+ площадок',
      '90% роялти артисту',
      'Расширенная аналитика',
      'Питчинг в плейлисты',
      'ISRC + UPC коды',
      'Приоритетная поддержка',
      'Предзаказ и прелиз',
    ],
    featured: true,
    cta: 'Выбрать Стандарт',
  },
  {
    name: 'Премиум',
    price: '3 500\u20BD',
    period: 'за релиз',
    description: 'Для профессионалов, которым важен результат',
    features: [
      'Все из Стандарт',
      '95% роялти артисту',
      'Персональный менеджер',
      'Промо-кампания',
      'Стратегия продвижения',
      'Верификация в стримингах',
      'Canvas для Spotify',
      'Приоритетная модерация',
    ],
    cta: 'Выбрать Премиум',
  },
]

interface PricingProps {
  detailed?: boolean
}

export function Pricing({ detailed = false }: PricingProps) {
  return (
    <section id="pricing" className="px-6 py-16 md:py-[120px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <h2 className="text-[28px] font-semibold tracking-[-0.02em] text-[#0f172a] md:text-[40px]">
            Тарифы
          </h2>
          <p className="mt-4 text-[18px] leading-[1.7] text-[#64748b]">
            {detailed
              ? 'Выберите тариф, который подходит именно вам'
              : 'Простые и прозрачные цены'}
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {pricingPlans.map((plan) => (
            <div
              key={plan.name}
              className={cn(
                'relative flex flex-col rounded-2xl bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-md',
                plan.featured && 'border-2 border-[#2563eb] shadow-md md:scale-[1.02]'
              )}
            >
              {plan.featured && (
                <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-[#2563eb] px-4 py-1 text-[12px] font-semibold text-white">
                  Популярный
                </div>
              )}

              <div className="mb-6">
                <h3 className="text-[16px] font-semibold text-[#0f172a]">{plan.name}</h3>
                <p className="mt-1 text-[14px] text-[#64748b]">{plan.description}</p>
              </div>

              <div className="mb-8">
                <span className="text-[40px] font-bold tracking-[-0.02em] text-[#0f172a]">
                  {plan.price}
                </span>
                <span className="ml-1 text-[14px] text-[#94a3b8]">/ {plan.period}</span>
              </div>

              <ul className="mb-8 flex-1 space-y-3">
                {plan.features.map((feature) => (
                  <li key={feature} className="flex items-start gap-3">
                    <Check className="mt-0.5 size-4 shrink-0 text-[#2563eb]" />
                    <span className="text-[14px] leading-[1.5] text-[#64748b]">{feature}</span>
                  </li>
                ))}
              </ul>

              <Link
                to="/register"
                className={cn(
                  'flex items-center justify-center rounded-xl px-6 py-3 text-[14px] font-semibold transition-all duration-200',
                  plan.featured
                    ? 'bg-[#2563eb] text-white shadow-sm hover:bg-[#1d4ed8] hover:shadow-md'
                    : 'bg-[#f8f9fa] text-[#0f172a] hover:bg-[#f1f5f9]'
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
