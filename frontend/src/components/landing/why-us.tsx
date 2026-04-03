import { Heart, RefreshCw, Eye, Zap } from 'lucide-react'

const reasons = [
  {
    icon: Heart,
    title: 'Христианские ценности',
    description: 'Мы разделяем вашу веру и понимаем миссию вашей музыки. Каждый релиз — это служение.',
  },
  {
    icon: RefreshCw,
    title: 'Полный цикл',
    description: 'От записи до концерта — берем на себя дистрибуцию, промо, питчинг и менеджмент.',
  },
  {
    icon: Eye,
    title: 'Прозрачность',
    description: 'Честные условия, открытая аналитика и до 95% роялти артисту. Без скрытых платежей.',
  },
  {
    icon: Zap,
    title: 'Скорость',
    description: 'Релиз на площадках за 2-5 дней. Электронный договор за 2 минуты. Быстрая поддержка 24/7.',
  },
]

export function WhyUs() {
  return (
    <section className="bg-[#f5f5f5] px-6 py-20 md:py-[100px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <p className="mb-3 text-[13px] font-semibold uppercase tracking-[0.15em] text-[#7c3aed]">
            Преимущества
          </p>
          <h2 className="text-[28px] font-bold uppercase tracking-[-0.01em] text-[#0f0f0f] md:text-[42px]">
            Почему Future Label
          </h2>
        </div>

        <div className="grid gap-6 sm:grid-cols-2">
          {reasons.map((reason) => {
            const Icon = reason.icon
            return (
              <div
                key={reason.title}
                className="group flex gap-6 rounded-2xl bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
              >
                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-blue-600 text-white shadow-md">
                  <Icon className="size-6" />
                </div>
                <div>
                  <h3 className="mb-2 text-[18px] font-bold text-[#0f0f0f]">
                    {reason.title}
                  </h3>
                  <p className="text-[14px] leading-[1.7] text-[#6b7280]">
                    {reason.description}
                  </p>
                </div>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
