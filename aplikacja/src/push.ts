import { useEffect, useState } from 'react'
import { konto } from './konto'
import { isIOS, isStandalone } from './install'

// Przypomnienia od Wafla (Web Push). Serwer: wtyczka „Guliwera – powiadomienia Akademii” (REST akademia/v1/push*).
// Zgoda: osobna, pytamy dopiero po pierwszej ukończonej lekcji. Najwyżej 1 powiadomienie dziennie, nigdy 21–8.

export type PushStan = 'brak-wsparcia' | 'ios-dodaj' | 'zablokowane' | 'wylaczone' | 'wlaczone'
export const DOMYSLNA = '19:00'
export const GODZINY = Array.from({ length: 26 }, (_, i) => `${String(8 + Math.floor(i / 2)).padStart(2, '0')}:${i % 2 ? '30' : '00'}`)

const wspiera = () =>
  typeof window !== 'undefined' && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window

async function api(path: string, body?: unknown) {
  if (!konto) throw new Error('Zaloguj się, żeby włączyć przypomnienia.')
  const r = await fetch(konto.rest + path, {
    method: body === undefined ? 'GET' : 'POST',
    credentials: 'same-origin',
    headers: { 'X-WP-Nonce': konto.nonce, 'Content-Type': 'application/json' },
    body: body === undefined ? undefined : JSON.stringify(body),
  })
  const d = await r.json().catch(() => ({}))
  if (!r.ok) throw new Error((d as { message?: string }).message || `Błąd ${r.status}`)
  return d as Record<string, unknown>
}

const naBajty = (b64: string) => {
  const s = atob((b64 + '='.repeat((4 - (b64.length % 4)) % 4)).replace(/-/g, '+').replace(/_/g, '/'))
  return Uint8Array.from(s, (c) => c.charCodeAt(0))
}

async function rejestracja() {
  return navigator.serviceWorker.ready
}

export async function stanPush(): Promise<PushStan> {
  if (isIOS() && !isStandalone()) return 'ios-dodaj'
  if (!wspiera() || !konto) return 'brak-wsparcia'
  if (Notification.permission === 'denied') return 'zablokowane'
  const sub = await (await rejestracja()).pushManager.getSubscription()
  return sub && Notification.permission === 'granted' ? 'wlaczone' : 'wylaczone'
}

/** Włącza przypomnienia (pyta przeglądarkę o zgodę). */
export async function wlaczPush(godzina: string): Promise<PushStan> {
  if (!wspiera()) return 'brak-wsparcia'
  const zgoda = await Notification.requestPermission()
  if (zgoda !== 'granted') return zgoda === 'denied' ? 'zablokowane' : 'wylaczone'
  const { klucz } = (await api('push')) as { klucz: string }
  const reg = await rejestracja()
  const sub = (await reg.pushManager.getSubscription()) ??
    (await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: naBajty(klucz) }))
  await api('push/zapisz', { ...sub.toJSON(), godzina })
  try { localStorage.setItem('push-godzina', godzina) } catch { /* ignoruj */ }
  return 'wlaczone'
}

export async function wylaczPush(): Promise<PushStan> {
  if (!wspiera()) return 'brak-wsparcia'
  const sub = await (await rejestracja()).pushManager.getSubscription()
  if (sub) {
    await api('push/usun', { endpoint: sub.endpoint }).catch(() => undefined)
    await sub.unsubscribe()
  }
  return 'wylaczone'
}

export const ustawGodzine = (godzina: string) => {
  try { localStorage.setItem('push-godzina', godzina) } catch { /* ignoruj */ }
  return api('push/godzina', { godzina })
}
export const wyslijProbne = () => api('push/test', {})
export const zapisanaGodzina = () => { try { return localStorage.getItem('push-godzina') || DOMYSLNA } catch { return DOMYSLNA } }

export function usePush() {
  const [stan, setStan] = useState<PushStan | null>(null)
  useEffect(() => { stanPush().then(setStan).catch(() => setStan('brak-wsparcia')) }, [])
  return [stan, setStan] as const
}
