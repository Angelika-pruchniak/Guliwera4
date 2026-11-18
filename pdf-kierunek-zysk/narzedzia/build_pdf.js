// Kierunek Zysk v2 · HTML (../src) → PDF (../PDF) + mapa pól formularza (JSON) dla acroform.py
// Użycie: node build_pdf.js 03_Strategia_na_1_stronie.html 3_Strategia_na_1_stronie.pdf
const { chromium } = require('playwright');
const path = require('path'); const fs = require('fs');
const [src, out] = process.argv.slice(2);
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto('file://' + path.resolve(__dirname, '../src', src), { waitUntil: 'networkidle' });
  await page.evaluate(() => document.fonts.ready);
  // Wafel: SVG wstawiany inline, żeby cyfra „4” na bandanie używała osadzonego kroju Fraunces
  const svgs = {};
  for (const s of await page.$$eval('img.wafel', els => els.map(e => e.getAttribute('src'))))
    svgs[s] = fs.readFileSync(path.resolve(__dirname, '../src', s), 'utf8');
  await page.evaluate(svgs => document.querySelectorAll('img.wafel').forEach(img => {
    const d = document.createElement('div'); d.className = 'wafel'; d.setAttribute('style', img.getAttribute('style'));
    d.innerHTML = svgs[img.getAttribute('src')]; const s = d.querySelector('svg'); s.removeAttribute('width'); s.removeAttribute('height');
    s.style.width = '100%'; s.style.height = 'auto'; img.replaceWith(d); }), svgs);
  await page.evaluate(() => document.fonts.ready);
  const info = await page.evaluate(() => {
    const pages = [...document.querySelectorAll('section.page')];
    const fields = [], problems = [];
    pages.forEach((pg, i) => {
      const r0 = pg.getBoundingClientRect();
      if (pg.scrollHeight > pg.clientHeight + 1) problems.push(`strona ${i + 1}: treść wychodzi poza stronę (${pg.scrollHeight - pg.clientHeight}px)`);
      pg.querySelectorAll('.komorka,.pole-opis,.opis-k,.kom,.karta').forEach(el => {
        if (el.scrollHeight > el.clientHeight + 2) problems.push(`strona ${i + 1}: przepełniona ramka „${(el.innerText || '').slice(0, 40).replace(/\n/g, ' ')}”`);
      });
      const st = pg.querySelector('.stopka');
      if (st) { const sb = st.getBoundingClientRect();
        [...pg.children].filter(c => c !== st && !c.classList.contains('kolo') && !c.classList.contains('wafel') && !c.classList.contains('reka')).forEach(c => {
          const b = c.getBoundingClientRect(); if (b.bottom > sb.top + 1 && b.height > 0) problems.push(`strona ${i + 1}: element nachodzi na stopkę: ${(c.innerText || c.className).slice(0, 40).replace(/\n/g, ' ')}`); }); }
      pg.querySelectorAll('[data-f],[data-cb]').forEach(el => {
        const b = el.getBoundingClientRect();
        fields.push({ page: i, name: el.dataset.f || el.dataset.cb, type: el.dataset.f ? 'text' : 'check',
          multiline: !!el.dataset.f && b.height > 30, x: b.left - r0.left, y: b.top - r0.top, w: b.width, h: b.height,
          dark: !!el.closest('.ciemna') });
      });
    });
    return { fields, problems, pages: pages.length };
  });
  const outPath = path.resolve(__dirname, '../PDF', out);
  await page.pdf({ path: outPath, preferCSSPageSize: true, printBackground: true, outline: true, tagged: true });
  fs.writeFileSync(outPath.replace(/\.pdf$/, '.pola.json'), JSON.stringify(info.fields, null, 1));
  console.log(`OK ${out}: ${info.pages} stron, ${info.fields.length} pól`);
  info.problems.forEach(p => console.log('UWAGA', p));
  await browser.close();
})();
