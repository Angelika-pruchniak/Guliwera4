import { useSyncExternalStore } from 'react'

// Konto na guliwera4.pl. Wtyczka WordPress wstrzykuje window.GK_AK do index.html,
// gdy użytkownik jest zalogowany. Postęp do rabatu liczy serwer (nie da się go podbić w przeglądarce).

type Cfg = { rest: string; nonce: string; imie: string; sklep: string; koszyk: string; wyloguj: string }
export type Kupon = { kod: string; procent: number; wazneDo: string; aktualny: boolean }
export type StatusRabatu = {
  lekcje: number
  wszystkie: number
  aktywneDni: number
  okno: number
  mnoznik: number
  rabat: number
  rabatMax: number
  dzisLekcji: number
  limitDzienny: number
  kupon: Kupon | null
  nowyKuponOd: string | null
  moznaOdebrac: boolean
  zgoda: boolean
}

export const konto: Cfg | null = typeof window === 'undefined' ? null : (window as unknown as { GK_AK?: Cfg }).GK_AK ?? null

let status: StatusRabatu | null = null
let blad: string | null = null
const listeners = new Set<() => void>()
const emit = () => listeners.forEach((l) => l())
let snapshot: { status: StatusRabatu | null; blad: string | null } = { status, blad }
function set(s: StatusRabatu | null, b: string | null) {
  status = s
  blad = b
  snapshot = { status: s, blad: b }
  emit()
}

export function useRabat() {
  return useSyncExternalStore((l) => { listeners.add(l); return () => listeners.delete(l) }, () => snapshot)
}

async function call(path: string, body?: unknown): Promise<StatusRabatu> {
  if (!konto) throw new Error('Brak logowania')
  const r = await fetch(konto.rest + path, {
    method: body === undefined ? 'GET' : 'POST',
    credentials: 'same-origin',
    headers: { 'X-WP-Nonce': konto.nonce, 'Content-Type': 'application/json' },
    body: body === undefined ? undefined : JSON.stringify(body),
  })
  const data = await r.json().catch(() => ({}))
  if (!r.ok) throw new Error((data as { message?: string }).message || `Błąd ${r.status}`)
  return data as StatusRabatu
}

// Kolejka zdarzeń: gdy nie ma sieci, wyślemy je później.
const QKEY = 'akademia-kolejka'
type Zdarzenie = { t: 'lekcja'; id: string } | { t: 'aktywnosc' }
const kolejka = (): Zdarzenie[] => { try { return JSON.parse(localStorage.getItem(QKEY) || '[]') } catch { return [] } }
const zapisz = (q: Zdarzenie[]) => { try { localStorage.setItem(QKEY, JSON.stringify(q.slice(-50))) } catch { /* ignoruj */ } }

let trwa = false
export async function wyslijKolejke() {
  if (!konto || trwa) return
  trwa = true
  try {
    let q = kolejka()
    while (q.length) {
      const z = q[0]
      const s = await call(z.t === 'lekcja' ? 'lekcja' : 'aktywnosc', z.t === 'lekcja' ? { id: z.id } : {})
      set(s, null)
      q = kolejka().slice(1)
      zapisz(q)
    }
    if (!status) set(await call('status'), null)
  } catch (e) {
    // Nieznana lekcja (400) wyrzucamy z kolejki, resztę ponowimy później.
    if (String(e).includes('Nieznana')) zapisz(kolejka().slice(1))
    else set(status, 'Brak połączenia z guliwera4.pl. Postęp wyślemy później.')
  } finally {
    trwa = false
  }
}

export function zglosLekcje(id: string) { zapisz([...kolejka(), { t: 'lekcja', id }]); void wyslijKolejke() }
export function zglosAktywnosc() { zapisz([...kolejka(), { t: 'aktywnosc' }]); void wyslijKolejke() }

export async function odbierzKupon() {
  try { set(await call('kupon', {}), null) } catch (e) { set(status, (e as Error).message) }
}
export async function ustawZgode(zgoda: boolean) {
  try { set(await call('zgoda', { zgoda }), null) } catch (e) { set(status, (e as Error).message) }
}

// Ta sama reguła co we wtyczce (do podpowiedzi „ile brakuje”).
export const mnoznik = (a: number) => (a >= 14 ? 1 : a >= 8 ? 0.75 : a >= 4 ? 0.5 : 0)
export function rabat(l: number, n: number, a: number, max = 30, min = 5) {
  if (n <= 0) return 0
  const r = Math.floor(max * Math.min(1, l / n) * mnoznik(a) + 1e-9)
  return r >= min ? r : 0
}
