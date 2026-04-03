import { Upload, Settings, Headphones } from 'lucide-react'

const steps = [
  {
    step: '01',
    icon: Upload,
    title: 'Загрузите трек',
    description:
      'Загрузите аудиофайл, обложку и заполните метаданные — название, исполнитель, жанр.',
  },
  {
    step: '02',
    icon: Settings,
    title: 'Мы все оформим',
    description:
      'Проверим качество, подготовим релиз и отправим на все площадки. Вам ничего делать не нужно.',
  },
  {
    step: '03',
    icon: Headphones,
    title: 'Слушайте на площадках',
    description:
      'Ваш трек появится на Spotify, Apple Music, Яндекс Музыке и других за 2-5 дней.',
  },
]

export function Steps() {
  return (
    <section className="px-6 py-16 md:py-[120px]">
      <div className="mx-auto max-w-[1200px]">
        <div className="mb-16 text-center">
          <h2 className="text-[28px] font-semibold tracking-[-0.02em] text-[#0f172a] md:text-[40px]">
            Как это работает
          </h2>
          <p className="mt-4 text-[18px] leading-[1.7] text-[#64748b]">
            Три простых шага до вашего релиза
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {steps.map((item) => {
            const Icon = item.icon
            return (
              <div
                key={item.step}
                className="group rounded-2xl bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md"
              >
                <div className="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eff6ff] text-[#2563eb] transition-colors duration-300 group-hover:bg-[#2563eb] group-hover:text-white">
                  <Icon className="size-6" />
                </div>
                <div className="mb-3 text-[13px] font-semibold text-[#94a3b8]">
                  Шаг {item.step}
                </div>
                <h3 className="mb-2 text-[16px] font-semibold text-[#0f172a]">
                  {item.title}
                </h3>
                <p className="text-[14px] leading-[1.7] text-[#64748b]">
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
