import { Link } from '@tanstack/react-router'

export function Cta() {
  return (
    <section className="relative overflow-hidden px-6 py-20 md:py-[100px]">
      {/* Gradient background */}
      <div className="absolute inset-0 bg-gradient-to-br from-purple-700 via-violet-600 to-blue-600" />
      <div className="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-black/5" />
      {/* Grain texture */}
      <div className="pointer-events-none absolute inset-0 opacity-[0.03]" style={{
        backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E")`,
        backgroundSize: '128px 128px',
      }} />

      <div className="relative z-10 mx-auto max-w-[700px] text-center">
        <h2 className="text-[32px] font-extrabold uppercase leading-[1.1] text-white md:text-[52px]">
          Начни сегодня
        </h2>
        <p className="mx-auto mt-5 max-w-[480px] text-[16px] leading-[1.7] text-white/60 md:text-[18px]">
          Присоединяйся к артистам, которые уже выпускают музыку через Future Label
        </p>
        <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
          <Link
            to="/register"
            className="inline-flex w-full items-center justify-center rounded-full bg-white px-10 py-4 text-[14px] font-bold uppercase tracking-[0.06em] text-[#0f0f0f] shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl sm:w-auto"
          >
            Выпустить музыку
          </Link>
          <Link
            to="/login"
            className="inline-flex w-full items-center justify-center rounded-full border border-white/30 px-10 py-4 text-[14px] font-semibold uppercase tracking-[0.06em] text-white transition-all duration-300 hover:border-white/60 hover:bg-white/10 sm:w-auto"
          >
            Войти в кабинет
          </Link>
        </div>
      </div>
    </section>
  )
}
