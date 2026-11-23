// Test sklepu jak klient (bez składania zamówień). Uruchom: node narzedzia/test-sklepu.js [katalog_na_zrzut]
const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await (await b.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1280, height: 900 } })).newPage();
  const wyn = []; const blad = (m) => wyn.push('BŁĄD: ' + m);
  const idz = async (u) => { const r = await p.goto('https://guliwera4.pl' + u + (u.includes('?') ? '&' : '?') + 'nc=' + Date.now(), { waitUntil: 'load', timeout: 60000 }); await p.waitForTimeout(6000); return r; };
  for (const u of ['/', '/materialy/', '/koszyk/', '/moje-konto/', '/aplikacja/']) {
    const r = await idz(u); const t = await p.locator('body').innerText();
    if (r.status() !== 200) blad(u + ' status ' + r.status());
    if (t.includes('Wielkie rzeczy są na horyzoncie')) blad(u + ' pokazuje stronę „Wkrótce”');
  }
  await idz('/materialy/');
  const produkty = [...new Set(await p.$$eval('a[href*="/produkt/"]', a => a.map(x => x.pathname)))];
  for (const u of produkty) { const r = await idz(u); const h = await p.locator('h1').first().textContent().catch(() => ''); if (r.status() !== 200 || /horyzoncie/.test(h)) blad(u + ' produkt niedostępny (' + r.status() + ', ' + h + ')');
    const okl = await p.evaluate(() => { const e = document.querySelector('.gk-prod-okl .okl'); if (!e) return 'brak'; const c = getComputedStyle(e); return (c.backgroundImage !== 'none' || c.backgroundColor !== 'rgba(0, 0, 0, 0)') && e.offsetHeight > 150 ? 'ok' : 'bez stylu'; });
    if (okl !== 'ok') blad(u + ' okładka PDF: ' + okl); }
  await idz('/zamowienie/?add-to-cart=69'); await idz('/zamowienie/?add-to-cart=69');
  const t = await p.locator('body').innerText();
  if (t.includes('Nie ma dostępnych metod płatności')) blad('Zamówienie: brak metod płatności');
  if (!/BLIK/.test(t)) blad('Zamówienie: brak BLIK');
  if (!/39,00 zł/.test(t) || /78,00 zł/.test(t)) blad('Zamówienie: zła kwota / PDF 2× w koszyku');
  if (process.argv[2]) await p.screenshot({ path: process.argv[2] + '/zamowienie.png', fullPage: true });
  console.log(wyn.length ? wyn.join('\n') : 'Sklep OK: ' + produkty.length + ' produktów, zamówienie i płatności działają');
  await b.close(); process.exit(wyn.length ? 1 : 0);
})();
