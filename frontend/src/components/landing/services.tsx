import { Globe, Megaphone, ListMusic, TrendingUp, CalendarDays, BarChart3 } from 'lucide-react'

const services = [
  {
    icon: Globe,
    title: 'Дистрибуция',
    description:
      'Доставляем музыку на 100+ стриминговых платформ по всему миру, включая Spotify, Apple Music и Яндекс Музыку.',
  },
  {
    icon: Megaphone,
    title: 'Продвижение',
    description:
      'Запускаем таргетированные рекламные кампании в социальных сетях для максимального охвата аудитории.',
  },
  {
    icon: ListMusic,
    title: 'Питчинг',
    description:
      'Отправляем треки кураторам плейлистов на Spotify, Apple Music и других платформах для попадания в подборки.',
  },
  {
    icon: TrendingUp,
    title: 'Стратегия',
    description:
      'Разрабатываем индивидуальную стратегию развития карьеры артиста и увеличения аудитории.',
  },
  {
    icon: CalendarDays,
    title: 'Концерт-менеджмент',
    description:
      'Организуем концерты, фестивали и worship-события. Полный цикл от букинга до продакшена.',
  },
  {
    icon: BarChart3,
    title: 'Аналитика',
    description:
      'Предоставляем детальную аналитику по стримам, аудитории и доходам в реальном времени.',
  },
]

export function Services() {
  return (
    <section id="services" className="bg-[#f5f5f5] px-6 py-20 md:py-[100px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <p className="mb-3 text-[13px] font-semibold uppercase tracking-[0.15em] text-[#7c3aed]">
            Полный цикл
          </p>
          <h2 className="text-[28px] font-bold uppercase tracking-[-0.01em] text-[#0f0f0f] md:text-[42px]">
            360° сервис для артиста
          </h2>
          <p className="mx-auto mt-4 max-w-[500px] text-[16px] leading-[1.7] text-[#6b7280]">
            Все, что нужно для успешного релиза и развития карьеры
          </p>
        </div>

        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {services.map((service) => {
            const Icon = service.icon
            return (
              <div
                key={service.title}
                className="group rounded-2xl bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
              >
                <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#7c3aed]/10 text-[#7c3aed] transition-all duration-300 group-hover:bg-[#7c3aed] group-hover:text-white">
                  <Icon className="size-5" />
                </div>
                <h3 className="mb-2 text-[16px] font-bold uppercase tracking-[0.02em] text-[#0f0f0f]">
                  {service.title}
                </h3>
                <p className="text-[14px] leading-[1.7] text-[#6b7280]">
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
