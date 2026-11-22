import { useState } from 'react'
import { flatLessons } from '../content'
import { DARMOWE_DZIALY, konto, useRabat, wpiszKod } from '../konto'
import { useStore } from '../store'

export const PRO_KORZYSCI = [
  'wszystkie działy (od 3. wzwyż) i nowe lekcje dodawane w ciągu roku',
  'serca bez limitu: ucz się, ile chcesz',
  'certyfikat ukończenia Akademii Guliwera',
]

/** Zakup Akademii Pro albo aktywacja kodem firmy. Używane w Profilu i w okienku blokady działu. */
export function ProZakup({ compact = false }: { compact?: boolean }) {
  const { status: s, blad } = useRabat()
  const [kod, setKod] = useState('')
  const [czeka, setCzeka] = useState(false)
  const cena = s?.proCena ?? 249
  return (
    <div className="pro-zakup">
      {!compact && <p className="small">Działy 1–{DARMOWE_DZIALY} są za darmo. Akademia Pro daje:</p>}
      <ul className="pro-lista">{PRO_KORZYSCI.map((k) => <li key={k}>{k}</li>)}</ul>
      {konto && s?.proUrl
        ? <a className="btn" href={s.proUrl}>Kup dostęp na 12 miesięcy – {cena} zł</a>
        : <a className="btn" href="/moje-konto/?akademia=1">Zaloguj się, żeby kupić Pro</a>}
      <p className="muted small">Płatność jednorazowa, bez abonamentu. Dostęp włącza się sam po opłaceniu zamówienia.</p>
      {konto && (
        <form className="kod-form" onSubmit={async (e) => { e.preventDefault(); setCzeka(true); await wpiszKod(kod.trim()); setCzeka(false) }}>
          <input value={kod} onChange={(e) => setKod(e.target.value)} placeholder="Kod od firmy, np. FIRMA-AB12CD" aria-label="Kod od firmy" />
          <button className="btn small ghost" disabled={!kod.trim() || czeka}>Aktywuj kod</button>
        </form>
      )}
      {blad && <p className="muted small">{blad}</p>}
    </div>
  )
}

export function ProCard({ onCert }: { onCert: () => void }) {
  const { status: s } = useRabat()
  const st = useStore()
  const wszystkie = flatLessons.every((l) => st.done[l.id])
  return (
    <div className="card pro-card">
      <h3>Akademia Pro {s?.pro && <span className="pro-badge">aktywna</span>}</h3>
      {s?.pro ? (
        <>
          <p className="small">Masz pełny dostęp do {s.proDo}. Wszystkie działy odblokowane, serca bez limitu.</p>
          <button className="btn small" disabled={!wszystkie} onClick={onCert}>Pobierz certyfikat</button>
          {!wszystkie && <p className="muted small">Certyfikat odblokujesz po ukończeniu wszystkich lekcji.</p>}
        </>
      ) : <ProZakup />}
    </div>
  )
}
