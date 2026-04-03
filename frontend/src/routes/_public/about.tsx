import { createFileRoute } from '@tanstack/react-router'
import { Heart, Music, Users, Cross } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'

export const Route = createFileRoute('/_public/about')({
  component: AboutPage,
  head: () => ({
    meta: [
      { title: 'О нас — Future Label' },
      {
        name: 'description',
        content:
          'Future Label — христианский музыкальный лейбл. Наша миссия — помогать артистам делиться музыкой, прославляющей Бога.',
      },
    ],
  }),
})

function AboutPage() {
  return (
    <>
      {/* Hero */}
      <section className="px-4 py-20 sm:px-6 sm:py-28">
        <div className="mx-auto max-w-3xl text-center">
          <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-secondary px-4 py-1.5 text-sm text-muted-foreground">
            <Cross className="size-4 text-primary" />
            <span>Христианский лейбл</span>
          </div>
          <h1 className="text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
            Музыка, которая
            <br />
            <span className="text-primary">меняет сердца</span>
          </h1>
          <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
            Future Label — это христианский музыкальный лейбл, основанный с верой в то,
            что музыка способна прикасаться к сердцам людей и менять жизни.
            Мы помогаем артистам нести свое послание миру через цифровые платформы.
          </p>
        </div>
      </section>

      {/* Mission */}
      <section className="bg-secondary/50 px-4 py-20 sm:px-6 sm:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="mb-12 text-center">
            <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
              Наша миссия
            </h2>
          </div>

          <div className="grid gap-6 md:grid-cols-3">
            {missionItems.map((item) => {
              const Icon = item.icon
              return (
                <Card key={item.title} className="border-border/60 bg-white">
                  <CardContent className="pt-6 text-center">
                    <div className="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                      <Icon className="size-7" />
                    </div>
                    <h3 className="mb-2 text-lg font-semibold">{item.title}</h3>
                    <p className="text-sm leading-relaxed text-muted-foreground">
                      {item.description}
                    </p>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="px-4 py-20 sm:px-6 sm:py-24">
        <div className="mx-auto max-w-3xl">
          <h2 className="mb-12 text-center text-3xl font-bold tracking-tight sm:text-4xl">
            Наши ценности
          </h2>

          <div className="space-y-8">
            {values.map((value, index) => (
              <div key={value.title} className="flex gap-6">
                <div className="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                  {index + 1}
                </div>
                <div>
                  <h3 className="mb-1 text-lg font-semibold">{value.title}</h3>
                  <p className="text-muted-foreground">{value.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="bg-primary px-4 py-20 sm:px-6 sm:py-24">
        <div className="mx-auto max-w-3xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">
            Готовы поделиться своей музыкой?
          </h2>
          <p className="mt-4 text-lg text-white/80">
            Присоединяйтесь к Future Label и начните свой путь уже сегодня
          </p>
          <Button
            size="lg"
            className="mt-8 rounded-xl bg-white px-8 text-base text-primary hover:bg-white/90"
          >
            Начать бесплатно
          </Button>
        </div>
      </section>
    </>
  )
}

const missionItems = [
  {
    icon: Music,
    title: 'Распространять музыку',
    description:
      'Мы доставляем христианскую музыку на все крупнейшие стриминговые платформы, чтобы она была доступна каждому слушателю.',
  },
  {
    icon: Heart,
    title: 'Поддерживать артистов',
    description:
      'Мы создаем лучшие условия для творческих людей: справедливые роялти, профессиональная поддержка и индивидуальный подход.',
  },
  {
    icon: Users,
    title: 'Объединять сообщество',
    description:
      'Мы строим сообщество единомышленников — артистов, продюсеров и слушателей, объединенных верой и любовью к музыке.',
  },
]

const values = [
  {
    title: 'Вера и честность',
    description:
      'Мы строим наш бизнес на принципах честности и прозрачности. Каждое решение мы принимаем, руководствуясь нашими ценностями.',
  },
  {
    title: 'Качество и профессионализм',
    description:
      'Мы стремимся к высочайшему качеству во всем — от звука до обслуживания. Каждый релиз проходит тщательную проверку.',
  },
  {
    title: 'Забота об артистах',
    description:
      'Артисты — наша главная ценность. Мы создаем инструменты и условия, которые помогают им сосредоточиться на творчестве.',
  },
  {
    title: 'Доступность',
    description:
      'Мы верим, что каждый талантливый артист заслуживает возможность быть услышанным, независимо от бюджета или опыта.',
  },
]
