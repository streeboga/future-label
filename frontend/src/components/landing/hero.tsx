import { Link } from '@tanstack/react-router'

const platforms = ['Spotify', 'Apple Music', 'VK Музыка', 'Яндекс Музыка', 'YouTube Music']

export function Hero() {
  return (
    <section className="relative overflow-hidden">
      {/* Subtle radial gradient */}
      <div className="pointer-events-none absolute inset-0 bg-gradient-to-b from-white to-[#f8f9fa]" />
      <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_0%,rgba(37,99,235,0.05),transparent)]" />

      <div className="relative mx-auto max-w-[1200px] px-6 pb-24 pt-[140px] md:pb-[100px]">
        <div className="mx-auto max-w-[800px] text-center">
          {/* Badge */}
          <div className="mb-8 inline-flex items-center gap-2 rounded-full bg-[#eff6ff] px-4 py-1.5">
            <span className="text-[13px] font-medium text-[#2563eb]">
              Цифровая дистрибуция музыки
            </span>
          </div>

          {/* Headline */}
          <h1 className="text-[36px] font-bold leading-[1.1] tracking-[-0.03em] text-[#0f172a] md:text-[56px]">
            Выпусти свою музыку
            <br />
            на все площадки
          </h1>

          {/* Subtitle */}
          <p className="mx-auto mt-6 max-w-[540px] text-[18px] leading-[1.6] text-[#64748b] md:text-[20px]">
            Future Label — христианский музыкальный лейбл. Загрузите трек, и мы
            доставим его на все главные стриминговые платформы мира.
          </p>

          {/* CTAs */}
          <div className="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4">
            <Link
              to="/register"
              className="inline-flex w-full items-center justify-center rounded-xl bg-[#2563eb] px-8 py-3.5 text-[15px] font-semibold tracking-[-0.01em] text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#1d4ed8] hover:shadow-lg sm:w-auto"
            >
              Начать бесплатно
            </Link>
            <Link
              to="/about"
              className="inline-flex w-full items-center justify-center rounded-xl border border-black/[0.06] bg-white px-8 py-3.5 text-[15px] font-semibold tracking-[-0.01em] text-[#0f172a] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md sm:w-auto"
            >
              Узнать больше
            </Link>
          </div>

          {/* Platform names */}
          <div className="mt-20">
            <div className="flex flex-wrap items-center justify-center gap-x-2 gap-y-1">
              {platforms.map((name, i) => (
                <span key={name} className="flex items-center gap-2 text-[13px] text-[#94a3b8]">
                  {name}
                  {i < platforms.length - 1 && (
                    <span className="text-[#94a3b8]/50">·</span>
                  )}
                </span>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
