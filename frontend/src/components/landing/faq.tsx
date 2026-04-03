import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'

const faqItems = [
  {
    question: 'Сколько времени занимает публикация релиза?',
    answer:
      'Обычно релиз появляется на площадках в течение 2-5 рабочих дней после одобрения. Некоторые площадки, например Spotify и Apple Music, могут обработать релиз быстрее.',
  },
  {
    question: 'Какой процент роялти получает артист?',
    answer:
      'В зависимости от тарифа артист получает от 85% до 95% всех доходов от стриминга. Мы предлагаем одни из лучших условий на рынке.',
  },
  {
    question: 'Как происходит выплата роялти?',
    answer:
      'Мы выплачиваем роялти ежемесячно на вашу банковскую карту или расчетный счет. Минимальная сумма для вывода — 500 рублей.',
  },
  {
    question: 'Нужен ли мне договор?',
    answer:
      'Да, но мы максимально упростили процесс. Договор подписывается электронно прямо в личном кабинете за пару минут.',
  },
  {
    question: 'Могу ли я удалить свой релиз?',
    answer:
      'Да, вы можете в любой момент снять релиз с площадок через личный кабинет. Удаление обычно занимает до 48 часов.',
  },
  {
    question: 'Какие форматы файлов вы принимаете?',
    answer:
      'Для аудио: WAV (16-bit, 44.1kHz) — рекомендуемый формат. Для обложки: JPG или PNG, минимум 3000x3000 пикселей.',
  },
]

export function Faq() {
  return (
    <section id="faq" className="px-6 py-20 md:py-[100px]">
      <div className="mx-auto max-w-[700px]">
        <div className="mb-16 text-center">
          <p className="mb-3 text-[13px] font-semibold uppercase tracking-[0.15em] text-[#7c3aed]">
            FAQ
          </p>
          <h2 className="text-[28px] font-bold uppercase tracking-[-0.01em] text-[#0f0f0f] md:text-[42px]">
            Частые вопросы
          </h2>
        </div>

        <Accordion type="single" collapsible className="w-full">
          {faqItems.map((item, index) => (
            <AccordionItem
              key={index}
              value={`item-${index}`}
              className="border-b border-black/[0.06] py-1"
            >
              <AccordionTrigger className="text-left text-[16px] font-semibold text-[#0f0f0f] hover:no-underline">
                {item.question}
              </AccordionTrigger>
              <AccordionContent className="text-[14px] leading-[1.7] text-[#6b7280]">
                {item.answer}
              </AccordionContent>
            </AccordionItem>
          ))}
        </Accordion>
      </div>
    </section>
  )
}
