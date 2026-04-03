import { Link } from '@tanstack/react-router'

const platforms = [
  'Spotify',
  'Apple Music',
  'VK Музыка',
  'Яндекс Музыка',
  'YouTube Music',
  'Deezer',
  'Tidal',
]

export function Hero() {
  return (
    <section className="relative overflow-hidden bg-white">
      {/* Subtle gradient background */}
      <div
        className="pointer-events-none absolute inset-0"
        style={{
          background:
            'radial-gradient(ellipse 70% 50% at 50% 0%, rgba(24, 95, 165, 0.06) 0%, transparent 100%)',
        }}
      />

      <div className="relative mx-auto max-w-[1120px] px-6 pb-24 pt-20 sm:pb-32 sm:pt-28 lg:pb-40 lg:pt-32">
        <div className="mx-auto max-w-[720px] text-center">
          {/* Badge */}
          <div className="mb-8 inline-flex items-center gap-2 rounded-full border border-[#e5e5e3] bg-white px-4 py-1.5">
            <span
              className="inline-block h-[6px] w-[6px] rounded-full"
              style={{ backgroundColor: '#185FA5' }}
            />
            <span className="text-[13px] font-medium text-[#6b6b6b]">
              Цифровая дистрибуция музыки
            </span>
          </div>

          {/* Headline */}
          <h1
            className="text-[40px] font-bold leading-[1.1] text-[#1a1a1a] sm:text-[52px] lg:text-[60px]"
            style={{ letterSpacing: '-0.03em' }}
          >
            Выпусти свою музыку
            <br />
            <span style={{ color: '#185FA5' }}>на все площадки</span>
          </h1>

          {/* Subtitle */}
          <p className="mx-auto mt-6 max-w-[540px] text-[17px] leading-[1.6] text-[#6b6b6b] sm:text-[19px]">
            Future Label — христианский музыкальный лейбл. Загрузите трек, и мы
            доставим его на все главные стриминговые платформы мира.
          </p>

          {/* CTAs */}
          <div className="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4">
            <Link
              to="/register"
              className="inline-flex w-full items-center justify-center rounded-lg px-9 py-3.5 text-[15px] font-semibold text-white shadow-sm transition-all duration-200 hover:shadow-md sm:w-auto"
              style={{
                backgroundColor: '#185FA5',
                letterSpacing: '-0.01em',
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.backgroundColor = '#0C447C'
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.backgroundColor = '#185FA5'
              }}
            >
              Начать бесплатно
            </Link>
            <Link
              to="/about"
              className="inline-flex w-full items-center justify-center rounded-lg border border-[#e5e5e3] bg-white px-9 py-3.5 text-[15px] font-semibold text-[#1a1a1a] transition-all duration-200 hover:border-[#d0d0ce] hover:bg-[#f7f7f5] sm:w-auto"
              style={{ letterSpacing: '-0.01em' }}
            >
              Узнать больше
            </Link>
          </div>

          {/* Platform badges */}
          <div className="mt-20">
            <p className="mb-5 text-[13px] font-medium uppercase tracking-[0.06em] text-[#999]">
              Ваша музыка появится на площадках
            </p>
            <div className="flex flex-wrap items-center justify-center gap-2.5">
              {platforms.map((name) => (
                <span
                  key={name}
                  className="rounded-full border border-[#e5e5e3] bg-[#fafaf9] px-4 py-1.5 text-[13px] font-medium text-[#6b6b6b] transition-colors duration-200 hover:border-[#d0d0ce] hover:text-[#1a1a1a]"
                >
                  {name}
                </span>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
