// Odświeża src/components/wafelSvg.ts z brand/wafel/generuj-wafla.js (Wafel: maskotka Guliwera 4).
const { wafel } = require('../brand/wafel/generuj-wafla.js');
const fs = require('fs'), path = require('path');
const MAPA = { happy: 'siedzi', cheer: 'radosc', sad: 'smutny', think: 'mysli', wave: 'macha', sleep: 'spi' };
let o = '// Wygenerowane z brand/wafel/generuj-wafla.js (node aplikacja/generuj-wafla-svg.cjs). Nie edytuj ręcznie.\nexport const WAFEL_SVG = {\n';
for (const [k, v] of Object.entries(MAPA)) o += `  ${k}: ${JSON.stringify(wafel(v).replace(/ width="400" height="450"/, '').replace(/\n\s*/g, ' '))},\n`;
fs.writeFileSync(path.join(__dirname, 'src/components/wafelSvg.ts'), o + '} as const\n');
console.log('src/components/wafelSvg.ts');
