import { beforeEach, describe, expect, it, vi } from 'vitest'
import { flatLessons } from './content'
import {
  completeLesson, currentLesson, mergeRemote, toRemote, dayDiff, getState, lessonStatus, levelOf, passTest,
  resetAll, update, visibleStreak,
} from './store'

const sum = { exerciseIds: ['u1l1-1'], firstTryWrong: [], maxCombo: 3 }

describe('logika gry', () => {
  beforeEach(() => { vi.useRealTimers(); resetAll() })

  it('liczy różnicę dni', () => {
    expect(dayDiff('2026-02-28', '2026-03-01')).toBe(1)
    expect(dayDiff('2026-11-05', '2026-11-05')).toBe(0)
  })

  it('poziomy rosną z XP', () => {
    expect(levelOf(0).name).toBe('Liliput')
    expect(levelOf(60).name).toBe('Praktykant')
    expect(levelOf(99999).next).toBeNull()
  })

  it('seria rośnie dzień po dniu i zeruje się po przerwie', () => {
    vi.useFakeTimers()
    vi.setSystemTime(new Date(2026, 10, 1, 12))
    completeLesson('u1l1', sum)
    expect(getState().streak).toBe(1)
    vi.setSystemTime(new Date(2026, 10, 2, 12))
    const r = completeLesson('u1l2', sum)
    expect(r.streakUp).toBe(true)
    expect(getState().streak).toBe(2)
    completeLesson('u1l3', sum) // ten sam dzień
    expect(getState().streak).toBe(2)
    vi.setSystemTime(new Date(2026, 10, 5, 12))
    expect(visibleStreak(getState())).toBe(0)
    completeLesson('u2l1', sum)
    expect(getState().streak).toBe(1)
  })

  it('zamrożenie ratuje serię', () => {
    vi.useFakeTimers()
    vi.setSystemTime(new Date(2026, 10, 1, 12))
    completeLesson('u1l1', sum)
    update((s) => { s.freezes = 1 })
    vi.setSystemTime(new Date(2026, 10, 3, 12))
    expect(visibleStreak(getState())).toBe(1)
    completeLesson('u1l2', sum)
    expect(getState().streak).toBe(2)
    expect(getState().freezes).toBe(0)
  })

  it('lekcje odblokowują się po kolei, a test poziomujący przeskakuje działy', () => {
    const s0 = getState()
    expect(lessonStatus(s0, 0)).toBe('open')
    expect(lessonStatus(s0, 1)).toBe('locked')
    passTest(2, true)
    const s1 = getState()
    expect(lessonStatus(s1, 4)).toBe('open') // dział 2 odblokowany
    expect(flatLessons[currentLesson(s1)].id).toBe('u3l1')
    expect(s1.badges).toContain('placed')
  })

  it('błędy w lekcji nie blokują zaliczenia (brak serc)', () => {
    const r = completeLesson('u1l1', { ...sum, firstTryWrong: ['a', 'b', 'c', 'd', 'e', 'f'] })
    expect(r.xp).toBeGreaterThan(0)
    expect(lessonStatus(getState(), 1)).toBe('open')
  })

  it('postęp z konta łączy się z lokalnym i nic nie przepada', () => {
    completeLesson('u1l1', sum)
    const changed = mergeRemote({ onboarded: true, done: { u1l1: 1, u1l2: 2 }, xp: 5, lastDay: '2000-01-01', streak: 9 })
    const s = getState()
    expect(changed).toBe(true)
    expect(s.done).toEqual({ u1l1: 1, u1l2: 2 })
    expect(s.xp).toBeGreaterThan(5) // większa wartość zostaje
    expect(s.streak).toBe(1) // seria z urządzenia, które uczyło się później
    expect(s.onboarded).toBe(true)
    expect(mergeRemote(toRemote(s))).toBe(false)
  })

  it('lekcja bez błędów daje bonus XP', () => {
    const perfect = completeLesson('u1l1', sum)
    resetAll()
    const withMistake = completeLesson('u1l1', { ...sum, firstTryWrong: ['u1l1-1'] })
    expect(perfect.xp).toBeGreaterThan(withMistake.xp)
  })
})
