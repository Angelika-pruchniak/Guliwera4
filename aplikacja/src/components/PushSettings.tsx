import { useState } from 'react'
import { GODZINY, ustawGodzine, usePush, wlaczPush, wylaczPush, wyslijProbne, zapisanaGodzina } from '../push'

/** Profil → Ustawienia: włącz/wyłącz przypomnienia, pora, próbne powiadomienie. */
export function PushSettings() {
  const [stan, setStan] = usePush()
  const [godzina, setGodzina] = useState(zapisanaGodzina)
  const [info, setInfo] = useState('')
  if (!stan || stan === 'brak-wsparcia') return null
  const on = stan === 'wlaczone'
  const zmien = async (wl: boolean) => {
    setInfo('')
    try { setStan(wl ? await wlaczPush(godzina) : await wylaczPush()) } catch (e) { setInfo((e as Error).message) }
  }
  return (
    <>
      <label className="toggle">
        <span>Przypomnienia od Wafla <span className="muted small">(raz dziennie, nigdy 21–8)</span></span>
        <input type="checkbox" checked={on} disabled={stan === 'ios-dodaj' || stan === 'zablokowane'} onChange={(e) => zmien(e.target.checked)} />
      </label>
      {stan === 'ios-dodaj' && <div className="muted small">Na iPhonie: dodaj Akademię do ekranu głównego (Safari → Udostępnij → Do ekranu początkowego) i otwórz ją z ikony.</div>}
      {stan === 'zablokowane' && <div className="muted small">Powiadomienia są zablokowane w ustawieniach przeglądarki lub telefonu dla guliwera4.pl.</div>}
      {on && (
        <>
          <label className="toggle">
            <span>Pora przypomnienia</span>
            <select value={godzina} onChange={(e) => { setGodzina(e.target.value); ustawGodzine(e.target.value).then(() => setInfo('Zapisane.')).catch((x) => setInfo((x as Error).message)) }}>
              {GODZINY.map((g) => <option key={g} value={g}>{g}</option>)}
            </select>
          </label>
          <button className="btn ghost small" onClick={() => wyslijProbne().then((r) => setInfo(r.ok ? 'Wysłane! Sprawdź powiadomienia.' : 'Nie udało się wysłać.')).catch((x) => setInfo((x as Error).message))}>Wyślij próbne powiadomienie</button>
        </>
      )}
      {info && <div className="muted small">{info}</div>}
    </>
  )
}
