import { Globe, ListMusic, Megaphone, TrendingUp } from 'lucide-react'

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
    <section id="services" className="bg-[#f8f9fa] px-6 py-16 md:py-[120px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <h2 className="text-[28px] font-semibold tracking-[-0.02em] text-[#0f172a] md:text-[40px]">
            Наши услуги
          </h2>
          <p className="mt-4 text-[18px] leading-[1.7] text-[#64748b]">
            Все, что нужно для успешного релиза
          </p>
        </div>

        <div className="grid gap-6 sm:grid-cols-2">
          {services.map((service) => {
            const Icon = service.icon
            return (
              <div
                key={service.title}
                className="group rounded-2xl bg-white p-7 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md"
              >
                <div className="mb-5 flex h-11 w-11 items-center justify-center rounded-full bg-[#eff6ff] text-[#2563eb]">
                  <Icon className="size-5" />
                </div>
                <h3 className="mb-2 text-[16px] font-semibold text-[#0f172a]">
                  {service.title}
                </h3>
                <p className="text-[14px] leading-[1.7] text-[#64748b]">
                  {service.description}
                </p>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
