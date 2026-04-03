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
    question: 'Могу ли я загрузить музыку бесплатно?',
    answer:
      'Да, у нас есть бесплатный тариф, который позволяет загружать синглы на 50+ площадок. Вы получаете 85% от всех доходов.',
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
    <section className="bg-secondary/50 px-4 py-20 sm:px-6 sm:py-24">
      <div className="mx-auto max-w-3xl">
        <div className="mb-12 text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Частые вопросы
          </h2>
          <p className="mt-3 text-lg text-muted-foreground">
            Ответы на популярные вопросы артистов
          </p>
        </div>

        <Accordion type="single" collapsible className="w-full">
          {faqItems.map((item, index) => (
            <AccordionItem key={index} value={`item-${index}`}>
              <AccordionTrigger className="text-left text-base font-medium">
                {item.question}
              </AccordionTrigger>
              <AccordionContent className="text-muted-foreground">
                {item.answer}
              </AccordionContent>
            </AccordionItem>
          ))}
        </Accordion>
      </div>
    </section>
  )
}
