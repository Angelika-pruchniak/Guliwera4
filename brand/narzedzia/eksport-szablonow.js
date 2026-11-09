// Eksport szablonów HTML do PNG w dokładnych wymiarach + podgląd z siatką.
// Uruchom z katalogu repozytorium: node brand/narzedzia/eksport-szablonow.js [nazwa-pliku-bez-.html]
// Wynik: brand/szablony/png/<nazwa>.png (okładka A4: ×2 = 300 dpi) i brand/szablony/png/siatka/<nazwa>.png
const { chromium } = require('playwright'); const fs = require('fs'); const path = require('path');
const DIR = path.join(__dirname, '..', 'szablony'); const OUT = path.join(DIR, 'png');
fs.mkdirSync(path.join(OUT, 'siatka'), { recursive: true });
const tylko = process.argv[2];
(async () => {
  const b = await chromium.launch();
  for (const f of fs.readdirSync(DIR).filter(f => f.endsWith('.html') && (!tylko || f.startsWith(tylko)))) {
    const n = f.replace('.html', '');
    const pg = await b.newPage({ viewport: { width: 2100, height: 2100 }, deviceScaleFactor: n.startsWith('okladka-pdf') ? 2 : 1 });
    for (const siatka of [false, true]) {
      await pg.goto('file://' + path.join(DIR, f) + (siatka ? '?siatka' : ''), { waitUntil: 'networkidle' });
      await pg.evaluate(() => document.fonts.ready);
      await pg.locator('.plotno').screenshot({ path: path.join(OUT, siatka ? 'siatka' : '', n + '.png') });
    }
    await pg.close(); console.log('✓', n);
  }
  await b.close();
})();
