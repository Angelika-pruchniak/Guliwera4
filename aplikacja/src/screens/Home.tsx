import { useEffect, useRef, useState } from 'react'
import { flatLessons, isExercise, units } from '../content'
import { InstallCard } from '../components/InstallCard'
import { Wafel } from '../components/Wafel'
import { Parcel, Paw, Progress } from '../components/ui'
import { fx } from '../fx'
import { DARMOWE_DZIALY } from '../konto'
import { chestReady, currentLesson, dayDiff, dayKey, lessonStatus, unitReached, useStore } from '../store'

type Props = {
  onLesson: (flatIndex: number) => void
  onSkip: (unitIndex: number) => void
  onChest: (unitIndex: number) => void
  pro?: boolean
}

/**
 * Trasa Wafla: każdy dział to etap spaceru, każda lekcja to przystanek (karta) na prostej trasie
 * ze śladami łap. Zaliczone lekcje dostają pieczątkę, na końcu etapu czeka paczka od Wafla.
 */
export function Home({ onLesson, onSkip, onChest, pro = false }: Props) {
  const s = useStore()
  const cur = currentLesson(s)
  const curRef = useRef<HTMLLIElement>(null)
  const todayXp = s.xpByDay[dayKey()] ?? 0
  const goalDone = todayXp >= s.dailyGoal
  // Dni bez nauki: od 2 dni przerwy Wafel leży i czeka na użytkownika.
  const gap = s.lastDay ? dayDiff(s.lastDay, dayKey()) : 0
  const missed = !goalDone && gap >= 2
  // Bezczynność na ekranie: po 45 s Wafel czeka, po 2 min drzemie; dotyk lub przewinięcie go budzi.
  const [idle, setIdle] = useState<'happy' | 'wait' | 'sleep'>('happy')
  useEffect(() => {
    let t1 = 0, t2 = 0
    const arm = () => {
      clearTimeout(t1); clearTimeout(t2); setIdle('happy')
      t1 = window.setTimeout(() => setIdle('wait'), 45_000)
      t2 = window.setTimeout(() => setIdle('sleep'), 120_000)
    }
    arm()
    const ev = ['pointerdown', 'scroll', 'keydown'] as const
    ev.forEach((e) => window.addEventListener(e, arm, { passive: true }))
    return () => { clearTimeout(t1); clearTimeout(t2); ev.forEach((e) => window.removeEventListener(e, arm)) }
  }, [])

  useEffect(() => {
    curRef.current?.scrollIntoView({ block: 'center', behavior: 'smooth' })
  }, [])

  const doneCount = flatLessons.filter((l) => s.done[l.id]).length

  return (
    <div className="home">
      <InstallCard />
      <div className="goal-card">
        <Wafel mood={goalDone ? 'cheer' : missed ? 'wait' : idle} size={58} />
        <div className="goal-text">
          <span className="hand">{goalDone ? 'Hau! Cel dnia zrobiony' : missed ? `Wafel czekał ${gap} dni` : 'Dzisiejszy spacer z Waflem'}</span>
          <b>{todayXp}/{s.dailyGoal} XP · {doneCount}/{flatLessons.length} lekcji</b>
          <Progress value={todayXp / s.dailyGoal} color="var(--coral)" />
        </div>
      </div>

      {units.map((u, ui) => {
        const reached = unitReached(s, ui)
        const lessons = flatLessons.filter((l) => l.unitIndex === ui)
        const ukonczone = lessons.filter((l) => s.done[l.id]).length
        const paczka = chestReady(s, ui)
        const otwarta = !!s.chests[u.id]
        return (
          <section key={u.id} className={`etap c-${u.color}`}>
            <header className="etap-head">
              <div className="etap-nr">
                <span>Etap</span>
                <b>{ui + 1}</b>
              </div>
              <div className="etap-txt">
                <h2>{u.title}{!pro && ui >= DARMOWE_DZIALY && <span className="unit-pro">PRO</span>}</h2>
                <div className="etap-sub">{u.subtitle}</div>
                <div className="etap-licznik">{ukonczone}/{lessons.length} przystanków</div>
              </div>
              {!reached && (
                <button className="btn small light" onClick={() => { fx('tap'); onSkip(ui) }}>Przeskocz</button>
              )}
            </header>

            <ol className="trasa">
              {lessons.map((l, i) => {
                const st = lessonStatus(s, l.flatIndex)
                const isCur = l.flatIndex === cur
                const times = s.done[l.id] ?? 0
                const zadan = l.items.filter(isExercise).length
                return (
                  <li key={l.id} ref={isCur ? curRef : undefined}
                    className={`przystanek ${st} ${isCur ? 'teraz' : ''}`}>
                    <div className="slad" aria-hidden>
                      {isCur ? <Wafel mood="wave" size={46} /> : st === 'done' ? <span className="slad-ok">✓</span> : st === 'locked' ? <span className="slad-lock" /> : <Paw size={20} />}
                    </div>
                    <button className="karta" disabled={st === 'locked'}
                      aria-label={`Lekcja ${i + 1}: ${l.title}${st === 'locked' ? ' (zablokowana)' : ''}`}
                      onClick={() => { fx('tap'); onLesson(l.flatIndex) }}>
                      <span className="karta-nr">Lekcja {i + 1}</span>
                      <span className="karta-tytul">{l.title}</span>
                      <span className="karta-meta">{st === 'locked' ? 'Najpierw poprzedni przystanek' : `${zadan} zadań · ok. ${Math.max(3, Math.round(zadan * 0.7))} min`}</span>
                      {isCur && <span className="karta-cta">{times > 0 ? 'Powtórz' : 'Ruszamy z Waflem'} →</span>}
                      {st === 'done' && !isCur && (
                        <span className="pieczatka" aria-label={`Zaliczona ${times} raz${times > 1 ? 'y' : ''}`}>
                          zaliczone{times > 1 ? ` ×${times}` : ''}
                        </span>
                      )}
                    </button>
                  </li>
                )
              })}
              <li className={`przystanek paczka ${paczka ? 'gotowa' : otwarta ? 'otwarta' : ''}`}>
                <div className="slad" aria-hidden><Parcel size={34} open={otwarta} /></div>
                <button className="karta" disabled={!paczka} onClick={() => onChest(ui)}>
                  <span className="karta-nr">Meta etapu</span>
                  <span className="karta-tytul">Paczka od Wafla</span>
                  <span className="karta-meta">{otwarta ? 'Otwarta. Smacznego!' : paczka ? 'Gotowa! Dotknij, żeby otworzyć' : 'Ukończ wszystkie przystanki etapu'}</span>
                </button>
              </li>
            </ol>
          </section>
        )
      })}
      <div className="path-end">
        <Wafel mood="think" size={80} />
        <p>Kolejne etapy w przygotowaniu. Wafel już węszy za nowymi tematami!</p>
      </div>
    </div>
  )
}
