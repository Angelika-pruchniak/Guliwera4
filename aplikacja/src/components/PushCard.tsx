import { useState } from 'react'
import { Wafel } from './Wafel'
import { GODZINY, usePush, wlaczPush, zapisanaGodzina } from '../push'
import { useStore } from '../store'

const UKRYJ = 'push-karta-ukryta'

/** Karta „Mam Ci przypominać?”: pokazujemy dopiero po pierwszej ukończonej lekcji. */
export function PushCard() {
  const s = useStore()
  const [stan, setStan] = usePush()
  const [godzina, setGodzina] = useState(zapisanaGodzina)
  const [blad, setBlad] = useState('')
  const [trwa, setTrwa] = useState(false)
  const [ukryta, setUkryta] = useState(() => { try { return localStorage.getItem(UKRYJ) === '1' } catch { return false } })
  const lekcje = Object.keys(s.done).length
  if (ukryta || lekcje < 1 || !stan || stan === 'wlaczone' || stan === 'brak-wsparcia' || stan === 'zablokowane') return null
  const ukryj = () => { setUkryta(true); try { localStorage.setItem(UKRYJ, '1') } catch { /* ignoruj */ } }

  // iPhone bez ikony na ekranie: wyjaśnia to karta instalacji (InstallCard) i Profil.
  if (stan === 'ios-dodaj') return null

  const wlacz = async () => {
    setTrwa(true); setBlad('')
    try {
      const st = await wlaczPush(godzina)
      setStan(st)
      if (st !== 'wlaczone') setBlad(st === 'zablokowane' ? 'Powiadomienia są zablokowane w ustawieniach przeglądarki.' : 'Bez zgody przeglądarki nie wyślemy przypomnień.')
    } catch (e) { setBlad((e as Error).message) } finally { setTrwa(false) }
  }

  return (
    <div className="push-card">
      <Wafel mood="wave" size={56} />
      <div className="push-text">
        <b>Mam Ci przypominać o lekcji?</b>
        <span>Raz dziennie, o wybranej porze, tylko gdy nie było jeszcze nauki. Nigdy w nocy. Wyłączysz w Profilu.</span>
        <div className="push-row">
          <label className="push-godz">o <select value={godzina} onChange={(e) => setGodzina(e.target.value)} aria-label="Godzina przypomnienia">
            {GODZINY.map((g) => <option key={g} value={g}>{g}</option>)}
          </select></label>
          <button className="btn small" disabled={trwa} onClick={wlacz}>{trwa ? 'Chwila…' : 'Włącz przypomnienia'}</button>
          <button className="btn ghost small" onClick={ukryj}>Nie teraz</button>
        </div>
        {blad && <span className="push-blad">{blad}</span>}
      </div>
    </div>
  )
}
