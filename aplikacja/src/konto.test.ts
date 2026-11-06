import { describe, expect, it } from 'vitest'
import { mnoznik, rabat } from './konto'

describe('rabat za naukę', () => {
  it('rośnie z lekcjami i wymaga regularności', () => {
    expect(rabat(24, 24, 14)).toBe(30)
    expect(rabat(24, 24, 10)).toBe(22)
    expect(rabat(12, 24, 10)).toBe(11)
    expect(rabat(24, 24, 3)).toBe(0) // bez regularności nie ma rabatu
    expect(rabat(3, 24, 14)).toBe(0) // poniżej 5% kuponu nie wydajemy
  })
  it('progi mnożnika', () => {
    expect([0, 4, 8, 14, 28].map(mnoznik)).toEqual([0, 0.5, 0.75, 1, 1])
  })
})

import { dzialZablokowany } from './konto'
describe('Akademia Pro', () => {
  const st = (pro: boolean) => ({ pro, darmoweDzialy: 2 } as unknown as Parameters<typeof dzialZablokowany>[0])
  it('działy 1–2 za darmo, reszta wymaga Pro', () => {
    expect(dzialZablokowany(st(false), 0)).toBe(false)
    expect(dzialZablokowany(st(false), 1)).toBe(false)
    expect(dzialZablokowany(st(false), 2)).toBe(true)
    expect(dzialZablokowany(st(true), 7)).toBe(false)
    expect(dzialZablokowany(null, 2)).toBe(true)
  })
})
