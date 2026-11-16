import { WAFEL_SVG } from './wafelSvg'

export type Mood = 'happy' | 'cheer' | 'sad' | 'think' | 'wave' | 'sleep'

/** Wafel – maskotka Guliwera 4 i Akademii: owczarek australijski (liliowy merle, koralowa bandana z „4”). */
export function Wafel({ mood = 'happy', size = 120, className = '' }: { mood?: Mood; size?: number; className?: string }) {
  return (
    <div
      className={`wafel wafel-${mood} ${className}`}
      style={{ width: size, height: size * 1.125 }}
      role="img"
      aria-label="Wafel, maskotka Akademii"
      dangerouslySetInnerHTML={{ __html: WAFEL_SVG[mood] }}
    />
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
