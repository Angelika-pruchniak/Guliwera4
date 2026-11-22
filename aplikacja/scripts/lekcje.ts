// Zapisuje listę identyfikatorów lekcji do dist/lekcje.json.
// Wtyczka WordPress używa jej, żeby zaliczać tylko prawdziwe lekcje.
import { writeFileSync } from 'node:fs'
import { flatLessons } from '../src/content'

writeFileSync(new URL('../dist/lekcje.json', import.meta.url), JSON.stringify(flatLessons.map((l) => l.id)))
console.log(`lekcje.json: ${flatLessons.length} lekcji`)
