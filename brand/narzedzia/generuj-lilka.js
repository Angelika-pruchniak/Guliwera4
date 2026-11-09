// Lilek w 6 nastrojach (SVG). Port komponentu aplikacja/src/components/Lilek.tsx. Uruchom: node brand/narzedzia/generuj-lilka.js
const fs = require('fs'), path = require('path'); const INK = '#3A2340';
function lilek(mood) {
  const closed = mood === 'cheer' || mood === 'sleep', dy = mood === 'think' ? -3 : mood === 'sad' ? 2 : 0, dx = mood === 'think' ? 2 : 0;
  let s = `<ellipse cx="60" cy="127" rx="30" ry="4" fill="${INK}" opacity=".12"/><ellipse cx="46" cy="122" rx="10" ry="5" fill="#E8512A"/><ellipse cx="74" cy="122" rx="10" ry="5" fill="#E8512A"/>`;
  if (mood === 'cheer') s += `<ellipse cx="17" cy="56" rx="7" ry="14" fill="#E8512A" transform="rotate(-28 17 56)"/><ellipse cx="103" cy="56" rx="7" ry="14" fill="#E8512A" transform="rotate(28 103 56)"/>`;
  else if (mood === 'wave') s += `<ellipse cx="20" cy="88" rx="7" ry="12" fill="#E8512A" transform="rotate(18 20 88)"/><ellipse cx="103" cy="56" rx="7" ry="14" fill="#E8512A" transform="rotate(28 103 56)"/>`;
  else s += `<ellipse cx="20" cy="88" rx="7" ry="12" fill="#E8512A" transform="rotate(18 20 88)"/><ellipse cx="100" cy="88" rx="7" ry="12" fill="#E8512A" transform="rotate(-18 100 88)"/>`;
  s += `<path d="M60 32C88 32 101 54 101 81C101 108 85 123 60 123C35 123 19 108 19 81C19 54 32 32 60 32Z" fill="#FF6B3D"/><ellipse cx="60" cy="100" rx="25" ry="19" fill="#FFF6EE"/><path d="M55 86h10l-2 4 4 16-7 6-7-6 4-16z" fill="#B9A5E3"/><path d="M55 86h10l-2 4h-6z" fill="#8F79C9"/><ellipse cx="34" cy="80" rx="6" ry="3.5" fill="#F4799F" opacity=".75"/><ellipse cx="86" cy="80" rx="6" ry="3.5" fill="#F4799F" opacity=".75"/>`;
  if (closed) s += mood === 'cheer' ? `<g stroke="${INK}" stroke-width="3.4" fill="none" stroke-linecap="round"><path d="M39 70Q46 61 53 70"/><path d="M67 70Q74 61 81 70"/></g>` : `<g stroke="${INK}" stroke-width="3" fill="none" stroke-linecap="round"><path d="M39 68Q46 73 53 68"/><path d="M67 68Q74 73 81 68"/></g>`;
  else s += `<ellipse cx="46" cy="67" rx="9" ry="10" fill="#fff"/><ellipse cx="74" cy="67" rx="9" ry="10" fill="#fff"/><circle cx="${47+dx}" cy="${69+dy}" r="4.8" fill="${INK}"/><circle cx="${73+dx}" cy="${69+dy}" r="4.8" fill="${INK}"/><circle cx="${48.6+dx}" cy="${67+dy}" r="1.6" fill="#fff"/><circle cx="${74.6+dx}" cy="${67+dy}" r="1.6" fill="#fff"/>`;
  if (mood === 'sad') s += `<g stroke="${INK}" stroke-width="2.6" stroke-linecap="round"><path d="M38 55l12 3"/><path d="M82 55l-12 3"/></g>`;
  if (mood === 'think') s += `<g stroke="${INK}" stroke-width="2.6" stroke-linecap="round" fill="none"><path d="M38 54q8-4 15 0"/><path d="M68 52q8-2 14 2"/></g>`;
  if (mood === 'happy' || mood === 'wave') s += `<path d="M51 83Q60 91 69 83" stroke="${INK}" stroke-width="3" fill="none" stroke-linecap="round"/>`;
  if (mood === 'cheer') s += `<path d="M48 80Q60 98 72 80Z" fill="${INK}"/><path d="M53 88Q60 95 67 88Q60 85 53 88Z" fill="#F4799F"/>`;
  if (mood === 'sad') s += `<path d="M52 88Q60 81 68 88" stroke="${INK}" stroke-width="3" fill="none" stroke-linecap="round"/>`;
  if (mood === 'think') s += `<circle cx="62" cy="85" r="3" fill="${INK}"/>`;
  if (mood === 'sleep') s += `<path d="M55 84q5 3 10 0" stroke="${INK}" stroke-width="2.6" fill="none" stroke-linecap="round"/>`;
  s += `<path d="M77 30C83 14 95 7 106 4C102 15 92 27 80 33Z" fill="#FFC84A"/><path d="M86 21C91 15 97 11 103 8" stroke="#E8A92E" stroke-width="1.5" fill="none"/><path d="M36 40C37 18 83 18 84 40Z" fill="#1E2A4A"/><path d="M37 36h46v5H37z" fill="#B9A5E3"/><path d="M14 42Q60 24 106 42Q98 50 60 45Q22 50 14 42Z" fill="#1E2A4A"/><circle cx="60" cy="40" r="3.4" fill="#FFC84A"/>`;
  if (mood === 'sleep') s += `<g fill="#7A6580"><path d="M90 50h8l-8 9h8" stroke="#7A6580" stroke-width="2" fill="none" stroke-linejoin="round"/><path d="M101 40h6l-6 7h6" stroke="#7A6580" stroke-width="1.6" fill="none" stroke-linejoin="round"/></g>`;
  if (mood === 'sad') s += `<path d="M84 74q3 5 0 8q-3-3 0-8z" fill="#B9A5E3"/>`;
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 132" width="600" height="660" role="img" aria-label="Lilek, maskotka Akademii Guliwera"><title>Lilek</title>${s}</svg>\n`;
}
const out = path.join(__dirname, '..', 'lilek');
const N = { happy: 'zadowolony', cheer: 'radosc', wave: 'macha', think: 'mysli', sad: 'smutny', sleep: 'spi' };
for (const [m, n] of Object.entries(N)) fs.writeFileSync(path.join(out, `lilek-${n}.svg`), lilek(m));
console.log('Lilek OK');
