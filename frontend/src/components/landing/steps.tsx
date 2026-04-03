import { Upload, Settings, Headphones } from 'lucide-react'

const steps = [
  {
    num: '01',
    icon: Upload,
    title: 'Загрузите трек',
    description:
      'Загрузите аудиофайл, обложку и заполните метаданные — название, исполнитель, жанр.',
  },
  {
    num: '02',
    icon: Settings,
    title: 'Мы все оформим',
    description:
      'Проверим качество, подготовим релиз и отправим на все площадки. Вам ничего делать не нужно.',
  },
  {
    num: '03',
    icon: Headphones,
    title: 'Слушайте на площадках',
    description:
      'Ваш трек появится на Spotify, Apple Music, Яндекс Музыке и других за 2-5 дней.',
  },
]

export function Steps() {
  return (
    <section className="px-6 py-20 md:py-[100px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <p className="mb-3 text-[13px] font-semibold uppercase tracking-[0.15em] text-[#7c3aed]">
            Как это работает
          </p>
          <h2 className="text-[28px] font-bold uppercase tracking-[-0.01em] text-[#0f0f0f] md:text-[42px]">
            Три шага до релиза
          </h2>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {steps.map((item) => {
            const Icon = item.icon
            return (
              <div
                key={item.num}
                className="group rounded-2xl bg-[#f5f5f5] p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
              >
                <div className="mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-violet-600 to-blue-600 text-white shadow-md">
                  <Icon className="size-6" />
                </div>
                <div className="mb-3 text-[13px] font-bold uppercase tracking-[0.1em] text-[#7c3aed]">
                  Шаг {item.num}
                </div>
                <h3 className="mb-2 text-[18px] font-bold text-[#0f0f0f]">
                  {item.title}
                </h3>
                <p className="text-[14px] leading-[1.7] text-[#6b7280]">
                  {item.description}
                </p>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
