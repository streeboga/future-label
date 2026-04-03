import { Globe, Percent, HeadphonesIcon, FileCheck } from 'lucide-react'

const reasons = [
  {
    icon: Globe,
    stat: '100+',
    title: 'площадок',
    description: 'Ваша музыка на всех главных стриминговых платформах мира',
  },
  {
    icon: Percent,
    stat: '80%',
    title: 'роялти',
    description: 'Одни из лучших условий на рынке для артистов',
  },
  {
    icon: HeadphonesIcon,
    stat: '24/7',
    title: 'поддержка',
    description: 'Мы всегда на связи и готовы помочь с любым вопросом',
  },
  {
    icon: FileCheck,
    stat: '2 мин',
    title: 'на договор',
    description: 'Электронное подписание документов за пару минут',
  },
]

export function WhyUs() {
  return (
    <section className="bg-[#f8f9fa] px-6 py-16 md:py-[120px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <h2 className="text-[28px] font-semibold tracking-[-0.02em] text-[#0f172a] md:text-[40px]">
            Почему Future Label
          </h2>
          <p className="mt-4 text-[18px] leading-[1.7] text-[#64748b]">
            Мы создаем лучшие условия для артистов
          </p>
        </div>

        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
          {reasons.map((reason) => {
            const Icon = reason.icon
            return (
              <div key={reason.title} className="text-center">
                <div className="mx-auto mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eff6ff] text-[#2563eb]">
                  <Icon className="size-6" />
                </div>
                <div className="mb-1 text-[32px] font-bold tracking-[-0.02em] text-[#0f172a]">
                  {reason.stat}
                </div>
                <div className="mb-3 text-[14px] font-medium text-[#64748b]">
                  {reason.title}
                </div>
                <p className="text-[14px] leading-[1.7] text-[#94a3b8]">
                  {reason.description}
                </p>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
