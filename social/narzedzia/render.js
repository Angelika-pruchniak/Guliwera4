// Guliwera 4 · eksport HTML → PNG (Playwright). Uruchom z katalogu repozytorium: node social/narzedzia/render.js
const { chromium } = require('playwright');
const fs = require('fs'), path = require('path');
const dir = path.join(__dirname, '..', 'instagram', 'html');
const out = path.join(__dirname, '..', 'instagram', 'png');
fs.mkdirSync(out, { recursive: true });
const { pliki } = JSON.parse(fs.readFileSync(path.join(dir, 'lista.json'), 'utf8'));
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 1080, height: 1920 }, deviceScaleFactor: 1 });
  // fonty z Google Fonts są w systemie; zewnętrzny @import pomijamy, żeby nie czekać na sieć
  await p.route(/fonts\.(googleapis|gstatic)\.com/, r => r.abort());
  for (const f of pliki) {
    await p.goto('file://' + path.join(dir, f.n + '.html'));
    await p.evaluate(() => document.fonts.ready);
    const el = await p.$('.plotno');
    const dest = f.przezroczyste ? path.join(out, 'rolki', f.n + '.png') : path.join(out, f.n.startsWith('r') ? 'rolki' : 'karuzele', f.n + '.png');
    fs.mkdirSync(path.dirname(dest), { recursive: true });
    await el.screenshot({ path: dest, omitBackground: !!f.przezroczyste });
  }
  await b.close();
  console.log('PNG:', pliki.length);
})();
