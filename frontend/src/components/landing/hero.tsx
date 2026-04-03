import { Button } from '@/components/ui/button'
import { Music, Headphones, Radio, Disc3, Play } from 'lucide-react'

const platforms = [
  { name: 'Spotify', icon: Disc3 },
  { name: 'Apple Music', icon: Music },
  { name: 'VK Музыка', icon: Headphones },
  { name: 'Яндекс Музыка', icon: Play },
  { name: 'YouTube Music', icon: Radio },
]

export function Hero() {
  return (
    <section className="relative overflow-hidden bg-white px-4 py-20 sm:px-6 sm:py-28 lg:py-36">
      {/* Subtle background gradient */}
      <div className="pointer-events-none absolute inset-0 bg-gradient-to-b from-[#E6F1FB]/40 to-transparent" />

      <div className="relative mx-auto max-w-4xl text-center">
        <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-secondary px-4 py-1.5 text-sm text-muted-foreground">
          <Music className="size-4 text-primary" />
          <span>Цифровая дистрибуция музыки</span>
        </div>

        <h1 className="text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
          Выпусти свою музыку
          <br />
          <span className="text-primary">на все площадки</span>
        </h1>

        <p className="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground sm:text-xl">
          Future Label — христианский музыкальный лейбл. Загрузите трек, и мы доставим
          его на все главные стриминговые платформы мира.
        </p>

        <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
          <Button size="lg" className="w-full rounded-xl px-8 text-base sm:w-auto">
            Начать бесплатно
          </Button>
          <Button variant="outline" size="lg" className="w-full rounded-xl px-8 text-base sm:w-auto">
            Узнать больше
          </Button>
        </div>

        {/* Platform logos */}
        <div className="mt-16">
          <p className="mb-6 text-sm font-medium text-muted-foreground">
            Ваша музыка появится на площадках
          </p>
          <div className="flex flex-wrap items-center justify-center gap-6 sm:gap-10">
            {platforms.map((platform) => {
              const Icon = platform.icon
              return (
                <div
                  key={platform.name}
                  className="flex items-center gap-2 text-muted-foreground/60 transition-colors hover:text-muted-foreground"
                >
                  <Icon className="size-5" />
                  <span className="text-sm font-medium">{platform.name}</span>
                </div>
              )
            })}
          </div>
        </div>
      </div>
    </section>
  )
}
