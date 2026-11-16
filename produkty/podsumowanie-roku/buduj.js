// Buduje zeszyt „Pochwal się! Podsumowanie roku” do dist/:
//   1) HTML ze strony.js, 2) PDF A4 przez Playwright (Chromium), 3) pola formularza (pdf-lib) w miejscach
//   oznaczonych data-pole / data-check, 4) podgląd PNG wybranych stron (--podglad).
// Użycie: ./pobierz-fonty.sh && npm i && node buduj.js [--podglad]
const fs = require('fs'), path = require('path');
const { chromium } = require('playwright');
const { PDFDocument, rgb } = require('pdf-lib');
const fontkit = require('@pdf-lib/fontkit');
const { html, ROK } = require('./strony.js');

const KAT = __dirname, DIST = path.join(KAT, 'dist');
const NAZWA = `Pochwal-sie-Podsumowanie-roku-${ROK}-Guliwera4.pdf`;
const PODGLAD = [1, 8, 15]; // numery stron do podglądu w repo

(async () => {
  fs.mkdirSync(DIST, { recursive: true });
  const plikHtml = path.join(DIST, 'zeszyt.html');
  fs.writeFileSync(plikHtml, html().replace('href="styl.css"', 'href="../styl.css"'));

  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 794, height: 1123 } });
  await p.goto('file://' + plikHtml, { waitUntil: 'networkidle' });
  await p.evaluate(() => document.fonts.ready);

  // pozycje pól względem swojej strony (px CSS; 1 px = 0.75 pt)
  const pola = await p.evaluate(() => {
    const strony = [...document.querySelectorAll('.strona')];
    const wynik = [];
    document.querySelectorAll('[data-pole],[data-check]').forEach(el => {
      const s = el.closest('.strona'), i = strony.indexOf(s);
      const rs = s.getBoundingClientRect(), r = el.getBoundingClientRect();
      wynik.push({
        id: el.dataset.pole || el.dataset.check, typ: el.dataset.check ? 'check' : 'tekst',
        multi: el.hasAttribute('data-multi'), duze: el.hasAttribute('data-duze'),
        jasne: !!el.closest('[style*="--gr"][style*="color:#fff"]'),
        strona: i, x: r.left - rs.left, y: r.top - rs.top, w: r.width, h: r.height,
      });
    });
    return { wynik, liczba: strony.length };
  });
  const ids = new Set();
  for (const f of pola.wynik) { if (ids.has(f.id)) throw new Error('Powtórzone pole: ' + f.id); ids.add(f.id); }

  const pdfBuf = await p.pdf({ preferCSSPageSize: true, printBackground: true });

  if (process.argv.includes('--podglad')) {
    const dir = path.join(KAT, 'podglad'); fs.mkdirSync(dir, { recursive: true });
    const strony = await p.$$('.strona');
    for (const n of PODGLAD) {
      await strony[n - 1].screenshot({ path: path.join(dir, `strona-${String(n).padStart(2, '0')}.png`) });
    }
  }
  await b.close();

  // pola formularza
  const doc = await PDFDocument.load(pdfBuf);
  doc.registerFontkit(fontkit);
  const font = await doc.embedFont(fs.readFileSync(path.join(KAT, 'fonty/DMSans-400-normal.ttf')), { subset: false });
  const form = doc.getForm();
  const strony = doc.getPages();
  const PT = 0.75, atrament = rgb(0x3A / 255, 0x23 / 255, 0x40 / 255);
  for (const f of pola.wynik) {
    const pg = strony[f.strona], H = pg.getHeight();
    const x = f.x * PT, w = f.w * PT, h = f.h * PT, y = H - (f.y + f.h) * PT;
    if (f.typ === 'check') {
      const cb = form.createCheckBox(f.id);
      cb.addToPage(pg, { x, y, width: w, height: h, borderWidth: 0, backgroundColor: undefined, borderColor: undefined, textColor: rgb(0xE8 / 255, 0x51 / 255, 0x2A / 255) });
      continue;
    }
    const t = form.createTextField(f.id);
    if (f.multi) t.enableMultiline();
    t.addToPage(pg, { x: x + 1, y: y + 1, width: w - 2, height: h - 2, font, borderWidth: 0, backgroundColor: undefined, borderColor: undefined, textColor: f.jasne ? rgb(1, 1, 1) : atrament });
    t.setFontSize(f.duze ? 16 : 10.5);
    if (f.duze) t.setAlignment(1);
  }
  form.updateFieldAppearances(font);
  doc.setTitle(`Pochwal się! Podsumowanie roku ${ROK}`);
  doc.setAuthor('Guliwera 4 · Angelika Pruchniak-Gąszczyk');
  doc.setSubject('Zeszyt ćwiczeń: podsumowanie roku, sukcesy, lekcje i energia na nowy rok');
  doc.setLanguage('pl-PL');
  fs.writeFileSync(path.join(DIST, NAZWA), await doc.save());
  console.log(`${pola.liczba} stron, ${pola.wynik.length} pól → dist/${NAZWA}`);
})();
