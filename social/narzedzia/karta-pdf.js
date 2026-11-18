// Karta prezentowa A5 → PDF + podgląd PNG. Uruchom z katalogu repozytorium: node social/narzedzia/karta-pdf.js
const { chromium } = require('playwright');
const path = require('path');
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 600, height: 800 }, deviceScaleFactor: 2 });
  await p.route(/fonts\.(googleapis|gstatic)\.com/, r => r.abort());
  const dir = path.join(__dirname, '..', 'prezent');
  await p.goto('file://' + path.join(dir, 'karta-prezentowa.html'));
  await p.evaluate(() => document.fonts.ready);
  await p.pdf({ path: path.join(dir, 'karta-prezentowa-A5.pdf'), width: '148mm', height: '210mm', printBackground: true, preferCSSPageSize: true });
  const s = await p.$$('.str');
  await s[0].screenshot({ path: path.join(dir, 'karta-podglad-1.png') });
  await s[1].screenshot({ path: path.join(dir, 'karta-podglad-2.png') });
  await b.close();
  console.log('ok');
})();
