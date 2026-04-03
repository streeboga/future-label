import { Globe, Percent, HeadphonesIcon, FileCheck } from 'lucide-react'

const reasons = [
  {
    icon: Globe,
    title: '100+ площадок',
    description: 'Ваша музыка на всех главных стриминговых платформах мира',
  },
  {
    icon: Percent,
    title: '80% роялти',
    description: 'Одни из лучших условий на рынке для артистов',
  },
  {
    icon: HeadphonesIcon,
    title: 'Поддержка 24/7',
    description: 'Мы всегда на связи и готовы помочь с любым вопросом',
  },
  {
    icon: FileCheck,
    title: 'Договор автоматом',
    description: 'Электронное подписание документов за пару минут',
  },
]

export function WhyUs() {
  return (
    <section className="px-4 py-20 sm:px-6 sm:py-24">
      <div className="mx-auto max-w-6xl">
        <div className="mb-12 text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Почему Future Label
          </h2>
          <p className="mt-3 text-lg text-muted-foreground">
            Мы создаем лучшие условия для артистов
          </p>
        </div>

        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
          {reasons.map((reason) => {
            const Icon = reason.icon
            return (
              <div key={reason.title} className="text-center">
                <div className="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                  <Icon className="size-7" />
                </div>
                <h3 className="mb-2 text-lg font-semibold">{reason.title}</h3>
                <p className="text-sm leading-relaxed text-muted-foreground">
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
