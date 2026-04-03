import { Upload, Settings, Headphones } from 'lucide-react'
import { Card, CardContent } from '@/components/ui/card'

const steps = [
  {
    step: '01',
    icon: Upload,
    title: 'Загрузите трек',
    description: 'Загрузите аудиофайл, обложку и заполните метаданные — название, исполнитель, жанр.',
  },
  {
    step: '02',
    icon: Settings,
    title: 'Мы все оформим',
    description: 'Проверим качество, подготовим релиз и отправим на все площадки. Вам ничего делать не нужно.',
  },
  {
    step: '03',
    icon: Headphones,
    title: 'Слушайте на площадках',
    description: 'Ваш трек появится на Spotify, Apple Music, Яндекс Музыке и других за 2-5 дней.',
  },
]

export function Steps() {
  return (
    <section className="bg-secondary/50 px-4 py-20 sm:px-6 sm:py-24">
      <div className="mx-auto max-w-6xl">
        <div className="mb-12 text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Как это работает
          </h2>
          <p className="mt-3 text-lg text-muted-foreground">
            Три простых шага до вашего релиза
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {steps.map((item) => {
            const Icon = item.icon
            return (
              <Card
                key={item.step}
                className="group relative border-border/60 bg-white transition-all hover:border-primary/20 hover:shadow-md"
              >
                <CardContent className="pt-6">
                  <div className="mb-4 flex items-center gap-4">
                    <div className="flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                      <Icon className="size-6" />
                    </div>
                    <span className="text-3xl font-bold text-border">{item.step}</span>
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
  )
}
