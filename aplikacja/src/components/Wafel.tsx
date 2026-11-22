export type Mood = 'happy' | 'cheer' | 'sad' | 'think' | 'wave' | 'sleep' | 'wait'

const ALT: Record<Mood, string> = {
  happy: 'Wafel się cieszy',
  cheer: 'Wafel bije brawo',
  sad: 'Wafel jest zmartwiony',
  wait: 'Wafel leży i czeka na Ciebie',
  think: 'Wafel się zastanawia',
  wave: 'Wafel macha łapą',
  sleep: 'Wafel się nudzi i drzemie',
}

/** Wafel – maskotka Guliwera 4 i Akademii: owczarek australijski z bandaną G4 (grafiki: brand/wafel-g4). */
export function Wafel({ mood = 'happy', size = 120, className = '' }: { mood?: Mood; size?: number; className?: string }) {
  return (
    <div className={`wafel wafel-${mood} ${className}`} style={{ width: size, height: size }}>
      <img
        src={`${import.meta.env.BASE_URL}wafel/${mood}.webp`}
        alt={ALT[mood]}
        width={size}
        height={size}
        style={{ width: '100%', height: '100%', objectFit: 'contain', display: 'block', borderRadius: '18%' }}
        draggable={false}
      />
    </div>
  )
}

export function Speech({ mood = 'happy', text, size = 84 }: { mood?: Mood; text: string; size?: number }) {
  return (
    <div className="speech">
      <Wafel mood={mood} size={size} />
      <div className="bubble">{text}</div>
    </div>
  )
}

const PRAISE = ['Brawo!', 'Świetnie!', 'Tak jest!', 'Jak z podręcznika!', 'Prezes byłby dumny!', 'Mistrzowsko!', 'Bingo!', 'Hau! Pięknie!']
const OOPS = ['Prawie!', 'Nic straconego!', 'Zerknij na wyjaśnienie.', 'Każdy Guliwer kiedyś się mylił.']
export const praise = () => PRAISE[Math.floor(Math.random() * PRAISE.length)]
export const oops = () => OOPS[Math.floor(Math.random() * OOPS.length)]
