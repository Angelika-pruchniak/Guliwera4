import { useEffect, useState } from 'react'
import { konto, mnoznik, odbierzKupon, rabat, ustawZgode, useRabat, wyslijKolejke } from '../konto'
import { Progress } from './ui'

const PROGI: [number, string][] = [[4, '×0,5'], [8, '×0,75'], [14, '×1']]

/** Rabat w sklepie guliwera4.pl za regularną naukę. */
export function RabatCard() {
  const { status: s, blad } = useRabat()
  const [kopiuj, setKopiuj] = useState(false)
  useEffect(() => { void wyslijKolejke() }, [])

  if (!konto) {
    return (
      <div className="card rabat">
        <h3>Rabat na materiały</h3>
        <p className="muted small">Zaloguj się na guliwera4.pl, żeby za regularną naukę dostać do 30% rabatu na PDF-y Kierunek Zysk.</p>
        <a className="btn small" href="/moje-konto/?akademia=1">Zaloguj się</a>
      </div>
    )
  }
  if (!s) return <div className="card rabat"><h3>Rabat na materiały</h3><p className="muted small">{blad ?? 'Wczytuję…'}</p></div>

  const nastepnyProg = PROGI.find(([d]) => s.aktywneDni < d)
  const dalej = rabat(s.lekcje + 1, s.wszystkie, s.aktywneDni)
  const k = s.kupon

  return (
    <div className="card rabat">
      <h3>Rabat na materiały <span className="rabat-proc">{s.rabat}%</span></h3>
      <p className="muted small">Rabat rośnie z każdą ukończoną lekcją, ale liczy się tylko przy regularnej nauce. Maksymalnie {s.rabatMax}% na PDF-y Kierunek Zysk.</p>

      <div className="rabat-wiersz"><span>Ukończone lekcje</span><b>{s.lekcje} / {s.wszystkie}</b></div>
      <Progress value={s.lekcje / Math.max(1, s.wszystkie)} color="var(--coral)" />
      <div className="rabat-wiersz"><span>Aktywne dni (ostatnie {s.okno})</span><b>{s.aktywneDni} · mnożnik ×{String(mnoznik(s.aktywneDni)).replace('.', ',')}</b></div>
      <Progress value={Math.min(1, s.aktywneDni / 14)} color="var(--lilac)" />

      <p className="small">
        {nastepnyProg
          ? <>Jeszcze <b>{nastepnyProg[0] - s.aktywneDni}</b> aktywnych dni do mnożnika {nastepnyProg[1]}.</>
          : <>Pełny mnożnik! Ucz się dalej, żeby go utrzymać.</>}
        {s.lekcje < s.wszystkie && dalej > s.rabat && <> Kolejna lekcja: {dalej}%.</>}
        {s.dzisLekcji >= s.limitDzienny && <> Dziś zaliczono już {s.limitDzienny} nowe lekcje do rabatu, kolejne policzymy jutro.</>}
      </p>

      {k && k.aktualny && (
        <div className="kupon">
          <span className="muted small">Twój kupon −{k.procent}% (ważny do {k.wazneDo})</span>
          <b className="kupon-kod">{k.kod}</b>
          <div className="row">
            <button className="btn small ghost" onClick={() => { void navigator.clipboard?.writeText(k.kod); setKopiuj(true) }}>{kopiuj ? 'Skopiowano' : 'Kopiuj kod'}</button>
            <a className="btn small" href={`${konto.koszyk}?gk_kupon=${encodeURIComponent(k.kod)}`}>Użyj w koszyku</a>
            <a className="btn small ghost" href={konto.sklep}>Wybierz PDF</a>
          </div>
        </div>
      )}
      {s.moznaOdebrac && !(k && k.aktualny) && (
        <button className="btn" onClick={() => void odbierzKupon()}>Odbierz kupon −{s.rabat}%</button>
      )}
      {!s.moznaOdebrac && s.rabat > 0 && s.nowyKuponOd && !(k && k.aktualny) && (
        <p className="muted small">Kolejny kupon od {s.nowyKuponOd}.</p>
      )}

      <label className="toggle">
        <span className="small">Chcę dostawać maile od Guliwera 4 (nowe lekcje, materiały, oferty)</span>
        <input type="checkbox" checked={s.zgoda} onChange={(e) => void ustawZgode(e.target.checked)} />
      </label>
      {blad && <p className="muted small">{blad}</p>}
    </div>
  )
}
