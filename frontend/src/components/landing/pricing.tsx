import { Check } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
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
    <section id="pricing" className="bg-secondary/50 px-4 py-20 sm:px-6 sm:py-24">
      <div className="mx-auto max-w-6xl">
        <div className="mb-12 text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Тарифы
          </h2>
          <p className="mt-3 text-lg text-muted-foreground">
            {detailed
              ? 'Выберите тариф, который подходит именно вам'
              : 'Простые и прозрачные цены'}
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {pricingPlans.map((plan) => (
            <Card
              key={plan.name}
              className={cn(
                'relative flex flex-col border-border/60 transition-all hover:shadow-md',
                plan.featured && 'border-primary shadow-lg ring-1 ring-primary/20'
              )}
            >
              {plan.featured && (
                <div className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-primary px-4 py-1 text-xs font-medium text-white">
                  Популярный
                </div>
              )}

              <CardHeader className="pb-2">
                <CardTitle className="text-xl">{plan.name}</CardTitle>
                <CardDescription>{plan.description}</CardDescription>
              </CardHeader>

              <CardContent className="flex-1">
                <div className="mb-6">
                  <span className="text-4xl font-bold">{plan.price}</span>
                  <span className="ml-1 text-muted-foreground">/ {plan.period}</span>
                </div>

                <ul className="space-y-3">
                  {plan.features.map((feature) => (
                    <li key={feature} className="flex items-start gap-3 text-sm">
                      <Check className="mt-0.5 size-4 shrink-0 text-primary" />
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>
              </CardContent>

              <CardFooter>
                <Button
                  className={cn(
                    'w-full rounded-xl',
                    plan.featured ? '' : 'bg-secondary text-secondary-foreground hover:bg-secondary/80'
                  )}
                  variant={plan.featured ? 'default' : 'secondary'}
                >
                  {plan.cta}
                </Button>
              </CardFooter>
            </Card>
          ))}
        </div>
      </div>
    </section>
  )
}
