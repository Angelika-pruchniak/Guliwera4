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
