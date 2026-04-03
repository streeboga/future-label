import { Globe, ListMusic, Megaphone, TrendingUp } from 'lucide-react'
import { Card, CardContent } from '@/components/ui/card'

const services = [
  {
    icon: Globe,
    title: 'Дистрибуция',
    description:
      'Доставляем вашу музыку на 100+ стриминговых платформ по всему миру, включая Spotify, Apple Music и Яндекс Музыку.',
  },
  {
    icon: ListMusic,
    title: 'Питчинг в плейлисты',
    description:
      'Отправляем ваши треки кураторам плейлистов для максимального охвата новых слушателей.',
  },
  {
    icon: Megaphone,
    title: 'Промо-кампании',
    description:
      'Запускаем таргетированные рекламные кампании в социальных сетях для продвижения ваших релизов.',
  },
  {
    icon: TrendingUp,
    title: 'Стратегия продвижения',
    description:
      'Разрабатываем индивидуальную стратегию развития вашей карьеры и увеличения аудитории.',
  },
]

export function Services() {
  return (
    <section id="services" className="px-4 py-20 sm:px-6 sm:py-24">
      <div className="mx-auto max-w-6xl">
        <div className="mb-12 text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Наши услуги
          </h2>
          <p className="mt-3 text-lg text-muted-foreground">
            Все, что нужно для успешного релиза
          </p>
        </div>

        <div className="grid gap-6 sm:grid-cols-2">
          {services.map((service) => {
            const Icon = service.icon
            return (
              <Card
                key={service.title}
                className="group border-border/60 transition-all hover:border-primary/20 hover:shadow-md"
              >
                <CardContent className="flex gap-4 pt-6">
                  <div className="flex size-11 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                    <Icon className="size-5" />
                  </div>
                  <div>
                    <h3 className="mb-1 font-semibold">{service.title}</h3>
                    <p className="text-sm leading-relaxed text-muted-foreground">
                      {service.description}
                    </p>
                  </div>
                </CardContent>
              </Card>
            )
          })}
        </div>
      </div>
    </section>
  )
}
