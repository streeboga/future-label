import { Link } from '@tanstack/react-router'

const platforms = [
  { name: 'Spotify', icon: 'M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z' },
  { name: 'Apple Music', icon: 'M23.997 6.124a9.23 9.23 0 00-.24-2.19c-.317-1.31-1.062-2.31-2.18-3.043A5.022 5.022 0 0019.7.283a10.58 10.58 0 00-1.564-.2C17.6.024 17.063.01 16.525.007 14.63-.003 12.736 0 10.841.003c-.668 0-1.336.01-2.003.045a10.075 10.075 0 00-1.665.218 4.914 4.914 0 00-1.748.725 4.774 4.774 0 00-1.455 1.56c-.336.6-.536 1.247-.63 1.93a9.854 9.854 0 00-.168 1.468c-.028.568-.04 1.136-.042 1.705-.006 1.897-.003 3.793 0 5.69.002.567.014 1.135.041 1.702.03.61.089 1.217.24 1.812.317 1.257 1.009 2.229 2.066 2.96.573.394 1.207.645 1.888.795.517.115 1.04.175 1.567.21.57.04 1.14.052 1.71.054 1.894.006 3.788.003 5.682 0 .568-.002 1.136-.014 1.703-.041.61-.03 1.217-.089 1.812-.24 1.259-.32 2.23-1.01 2.96-2.064.394-.573.645-1.207.795-1.89.115-.517.175-1.04.21-1.567.04-.57.052-1.14.054-1.71.006-1.894.003-3.788 0-5.682zM16.948 17.2a.832.832 0 01-.628.298.835.835 0 01-.2-.024 8.207 8.207 0 01-.605-.168l-.272-.088c-.252-.088-.508-.15-.77-.186a4.453 4.453 0 00-.708-.033c-.29.015-.576.066-.856.147-.14.04-.276.093-.426.144-.09.031-.17.013-.232-.058-.062-.071-.068-.157-.026-.236a.605.605 0 01.084-.11c.22-.237.478-.423.76-.573.27-.143.556-.247.85-.326V8.372l-5.872 1.608v7.074c0 .02-.002.04-.005.06-.015.25-.065.494-.157.727a2.2 2.2 0 01-.385.63c-.243.283-.538.49-.878.617-.235.088-.477.143-.724.171a4.39 4.39 0 01-.72.012 3.608 3.608 0 01-.606-.089c-.348-.079-.666-.22-.95-.424a1.8 1.8 0 01-.508-.534 1.3 1.3 0 01-.204-.558 1.362 1.362 0 01.056-.56c.081-.267.22-.5.41-.7.237-.253.518-.432.835-.553.27-.104.55-.168.838-.198.358-.037.714-.024 1.068.04.128.022.254.054.38.084V8.636c0-.092.017-.18.056-.262a.636.636 0 01.275-.297.716.716 0 01.214-.092l6.552-1.794a1.063 1.063 0 01.236-.042.374.374 0 01.294.115.353.353 0 01.105.258v9.348c0 .023-.002.045-.005.067a2.627 2.627 0 01-.156.724 2.2 2.2 0 01-.385.63c-.244.283-.54.49-.88.618-.234.088-.476.143-.724.17a4.389 4.389 0 01-.72.012 3.614 3.614 0 01-.605-.088 2.408 2.408 0 01-.95-.424 1.794 1.794 0 01-.507-.534 1.3 1.3 0 01-.204-.558 1.36 1.36 0 01.055-.56z' },
  { name: 'VK Музыка', icon: 'M15.07 2H8.93C3.33 2 2 3.33 2 8.93v6.14C2 20.67 3.33 22 8.93 22h6.14C20.67 22 22 20.67 22 15.07V8.93C22 3.33 20.67 2 15.07 2zm3.08 14.27h-1.71c-.65 0-.85-.52-2.01-1.7-1.02-.99-1.46-1.12-1.71-1.12-.35 0-.45.1-.45.58v1.56c0 .41-.13.66-1.21.66-1.79 0-3.78-1.09-5.18-3.11-2.1-2.97-2.68-5.2-2.68-5.65 0-.25.1-.48.58-.48h1.71c.43 0 .6.2.76.66.84 2.44 2.25 4.58 2.83 4.58.22 0 .32-.1.32-.65V9.52c-.07-1.11-.65-1.21-.65-1.61 0-.2.17-.4.44-.4h2.69c.36 0 .49.2.49.63v3.38c0 .36.16.49.26.49.22 0 .4-.13.81-.54 1.25-1.4 2.14-3.56 2.14-3.56.12-.25.32-.48.76-.48h1.71c.51 0 .63.27.51.63-.22 1.04-2.36 4.04-2.36 4.04-.19.3-.26.44 0 .78.19.25.81.78 1.23 1.25.76.85 1.34 1.57 1.5 2.06.17.49-.08.74-.56.74z' },
  { name: 'Яндекс Музыка', icon: 'M12 0C5.376 0 0 5.376 0 12s5.376 12 12 12 12-5.376 12-12S18.624 0 12 0zm2.797 18.781h-2.227V9.843c0-.344.016-.75.047-1.219h-.047c-.11.407-.219.719-.328.938l-4.64 9.22H6.14V5.218h2.204v8.766c0 .438-.016.86-.047 1.266h.07c.094-.312.203-.625.329-.937l4.64-9.095h1.46v14.563z' },
  { name: 'YouTube Music', icon: 'M12 0C5.376 0 0 5.376 0 12s5.376 12 12 12 12-5.376 12-12S18.624 0 12 0zm0 19.104c-3.924 0-7.104-3.18-7.104-7.104S8.076 4.896 12 4.896s7.104 3.18 7.104 7.104-3.18 7.104-7.104 7.104zm0-13.332c-3.432 0-6.228 2.796-6.228 6.228S8.568 18.228 12 18.228s6.228-2.796 6.228-6.228S15.432 5.772 12 5.772zM9.684 15.54V8.46L16.2 12l-6.516 3.54z' },
]

export function Hero() {
  return (
    <section className="landing-hero relative flex min-h-screen items-center justify-center overflow-hidden">
      {/* Gradient background */}
      <div className="absolute inset-0 bg-gradient-to-br from-violet-700 via-blue-600 to-purple-700" />
      {/* Secondary overlay for depth */}
      <div className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-black/10" />
      {/* Animated grain texture */}
      <div className="pointer-events-none absolute inset-0 opacity-[0.035]" style={{
        backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E")`,
        backgroundSize: '128px 128px',
      }} />

      <div className="relative z-10 mx-auto max-w-[1200px] px-6 py-32 text-center">
        {/* Label */}
        <p className="mb-6 text-[13px] font-medium uppercase tracking-[0.2em] text-white/60">
          Христианский музыкальный лейбл
        </p>

        {/* Main heading */}
        <h1 className="text-[56px] font-extrabold uppercase leading-[0.95] tracking-[-0.02em] text-white md:text-[80px] lg:text-[96px]">
          Future Label
        </h1>

        {/* Subtitle */}
        <p className="mx-auto mt-6 max-w-[600px] text-[16px] tracking-[0.15em] text-white/60 md:text-[18px]">
          Дистрибуция &middot; Промо &middot; Стратегия &middot; Концерты
        </p>

        {/* CTAs */}
        <div className="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
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

        {/* Platform logos */}
        <div className="mt-24 flex flex-wrap items-center justify-center gap-8">
          {platforms.map((p) => (
            <div
              key={p.name}
              className="flex items-center gap-2 text-white/30 transition-colors duration-300 hover:text-white/50"
              title={p.name}
            >
              <svg className="size-5" viewBox="0 0 24 24" fill="currentColor">
                <path d={p.icon} />
              </svg>
              <span className="hidden text-[12px] font-medium uppercase tracking-[0.08em] sm:inline">
                {p.name}
              </span>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
