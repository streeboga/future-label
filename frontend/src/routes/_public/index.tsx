import { createFileRoute } from '@tanstack/react-router'
import { Hero } from '@/components/landing/hero'
import { Steps } from '@/components/landing/steps'
import { Services } from '@/components/landing/services'
import { Pricing } from '@/components/landing/pricing'
import { WhyUs } from '@/components/landing/why-us'
import { Faq } from '@/components/landing/faq'

export const Route = createFileRoute('/_public/')({
  component: LandingPage,
  head: () => ({
    meta: [
      { title: 'Future Label — Цифровая дистрибуция музыки' },
      {
        name: 'description',
        content:
          'Христианский музыкальный лейбл. Выпустите свою музыку на Spotify, Apple Music, Яндекс Музыку и 100+ площадок.',
      },
    ],
  }),
})

function LandingPage() {
  return (
    <>
      <Hero />
      <Steps />
      <Services />
      <Pricing />
      <WhyUs />
      <Faq />
    </>
  )
}
