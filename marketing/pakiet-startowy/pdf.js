// PDF-y Pakietu startowego Kierunek Zysk. Użycie (z katalogu repo): node marketing/pakiet-startowy/pdf.js KATALOG_WYJSCIOWY
const { chromium } = require('playwright');
const fs = require('fs'), path = require('path');
const out = process.argv[2] || __dirname;
const logo = 'data:image/png;base64,' + fs.readFileSync(path.join(__dirname, '../../logo/ikona-512.png')).toString('base64');

const css = `
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=DM+Sans:opsz,wght@9..40,400;9..40,600;9..40,700&family=Caveat:wght@600&display=swap');
@page{size:A4;margin:0}
*{box-sizing:border-box}
body{margin:0;font-family:"DM Sans",Arial,sans-serif;color:#3A2340;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.str{width:210mm;height:297mm;padding:14mm 15mm 12mm;background:#FFF6EE;display:flex;flex-direction:column;position:relative;overflow:hidden}
.str::before{content:"";position:absolute;width:120mm;height:120mm;border-radius:50%;background:#FFD9E4;right:-45mm;top:-55mm}
.gl{display:flex;align-items:center;gap:10px;position:relative}
.gl img{width:34px;height:34px;border-radius:9px}
.gl b{font-family:Fraunces,Georgia,serif;font-size:15px;display:block;line-height:1.1}
.gl span{font-size:10px;color:#7A6580}
.pis{font-family:Caveat,cursive;color:#E8512A;font-size:24px;margin:9mm 0 0;position:relative}
h1{font-family:Fraunces,Georgia,serif;font-weight:700;font-size:34px;line-height:1.05;margin:1mm 0 3mm;position:relative}
.lead{font-size:12.5px;color:#7A6580;max-width:150mm;margin:0 0 6mm;position:relative;line-height:1.5}
.stopka{margin-top:auto;display:flex;justify-content:space-between;gap:8mm;font-size:9.5px;color:#7A6580;border-top:1px solid #F0DDCF;padding-top:3mm}
.stopka b{color:#E8512A}
.etyk{display:inline-block;background:#FFC84A;color:#3A2340;font-weight:700;font-size:10px;letter-spacing:.12em;padding:3px 9px;border-radius:999px}
`;

// 1) Strategia na 1 stronie – wypełniony przykład (fikcyjna firma)
const pola = [
  ['Dokąd idziemy', 'Cel na rok, liczbą', '2027: 360 tys. zł przychodu (+20%) przy marży co najmniej 45%. Rozpoznawalność w Lublinie i okolicy jako pracownia warsztatów dla firm.', '#FFD8C2'],
  ['Gdzie gramy', 'Klienci, produkty, kanały', 'Firmy z Lubelszczyzny (warsztaty integracyjne do 20 osób) i klienci indywidualni (zajęcia wieczorne). Sprzedaż: strona, Instagram, polecenia działów HR.', '#FFD9E4'],
  ['Czym wygrywamy', 'Dlaczego ktoś wybierze nas', 'Warsztat „pod klucz”: zajęcia, wypał i dostawa gotowych naczyń do biura w 14 dni. Jedyna pracownia w mieście z ofertą dla firm na fakturę.', '#EDE5FB'],
  ['Czego NIE robimy', 'Najtrudniejsze pole', 'Nie sprzedajemy w sklepach z rękodziełem. Nie robimy zamówień hurtowych poniżej 45% marży. Nie otwieramy drugiej lokalizacji w 2027.', '#FFEFC7'],
];
const miary = [['Przychód / mies.', '30 tys. zł'], ['Marża', '≥ 45%'], ['Warsztaty dla firm', '6 / mies.'], ['Polecenia', '≥ 30% klientów']];
const prior = ['Pakiet „warsztat dla firmy” z ceną za osobę i stroną z ofertą (do końca I kwartału)', 'Stała współpraca z 3 działami HR w Lublinie (spotkania co miesiąc)', 'Podniesienie ceny zajęć wieczornych o 10% od kwietnia (najpierw policzone w kalkulatorze zysku)'];

const strategia = `<div class="str">
<div class="gl"><img src="${logo}"><div><b>Guliwera 4</b><span>Pakiet startowy Kierunek Zysk</span></div></div>
<p class="pis">wypełniony przykład</p>
<h1>Strategia na 1 stronie</h1>
<p class="lead"><span class="etyk">PRZYKŁAD</span>&nbsp; Firma jest wymyślona: <b>Pracownia ceramiki „Glina”</b>, Lublin, 2 osoby. Zobacz, jak wygląda gotowa strategia, i spróbuj wypełnić jedno pole dla swojej firmy: „Czego NIE robimy”.</p>
<div class="siatka">${pola.map(([t, p, x, k]) => `<div class="pole" style="--k:${k}"><h2>${t}</h2><small>${p}</small><p>${x}</p></div>`).join('')}</div>
<div class="dol">
<div class="miary"><h2>Po czym poznamy postęp</h2><small>3–5 liczb sprawdzanych co miesiąc</small>
<table>${miary.map(([a, b]) => `<tr><td>${a}</td><td>${b}</td></tr>`).join('')}</table></div>
<div class="prior"><h2>3 priorytety na kwartał</h2><small>Tylko to, co przybliża cel</small>
<ol>${prior.map(p => `<li>${p}</li>`).join('')}</ol></div>
</div>
<div class="cta"><b>Chcesz zrobić to dla swojej firmy?</b> Pełny szablon <b>Strategia na 1 stronie</b> do wypełnienia dla własnej firmy, z instrukcją: 39 zł. <span>guliwera4.pl/materialy</span></div>
<div class="stopka"><span>Guliwera 4 · Angelika Pruchniak-Gąszczyk · ul. Guliwera 4, Lublin · kontakt@guliwera4.pl</span><span>Materiał bezpłatny, do użytku własnego</span></div>
</div>
<style>
.siatka{display:grid;grid-template-columns:1fr 1fr;gap:4mm;position:relative}
.pole{background:#fff;border-radius:14px;padding:4.5mm 5mm;border-top:5px solid var(--k);min-height:44mm}
.pole h2,.dol h2{font-family:Fraunces,Georgia,serif;font-size:17px;margin:0}
small{display:block;font-size:9.5px;color:#7A6580;text-transform:uppercase;letter-spacing:.1em;margin:1mm 0 2mm}
.pole p{margin:0;font-size:12px;line-height:1.5}
.dol{display:grid;grid-template-columns:.8fr 1.2fr;gap:4mm;margin-top:4mm}
.miary,.prior{background:#fff;border-radius:14px;padding:4.5mm 5mm}
table{width:100%;border-collapse:collapse;font-size:12px}td{padding:1.8mm 0;border-bottom:1px solid #F0DDCF}td:last-child{text-align:right;font-weight:700;color:#E8512A}
ol{margin:0;padding-left:5mm;font-size:12px;line-height:1.5}li{margin-bottom:1.5mm}
.cta{margin-top:5mm;background:#3A2340;color:#fff;border-radius:14px;padding:4.5mm 6mm;font-size:12px;line-height:1.5}.cta span{color:#FFC84A;font-weight:700;margin-left:4px}
</style>`;

// 2) Przegląd miesiąca w 15 minut – checklista
const pyt = [
  ['Liczby', 'Przychód, marża i gotówka na koncie: ile było, ile planowane? Jedna liczba, która Cię zaskoczyła.', 3],
  ['Co zadziałało', 'Które 20% działań dało najwięcej efektu? Co z tego robić częściej?', 2],
  ['Co nie zadziałało', 'Co zjadło czas albo pieniądze bez efektu? Dlaczego?', 2],
  ['Odłożone decyzje', 'Jaką decyzję odkładasz dłużej niż 2 tygodnie? Co jest potrzebne, żeby ją podjąć?', 2],
  ['Stop', 'Co przestajesz robić od jutra? (Jedna rzecz wystarczy.)', 1],
  ['3 priorytety', 'Na kolejny miesiąc: tylko to, co przybliża cel roczny.', 3],
  ['Lekcja miesiąca', 'Czego nauczył Cię ten miesiąc? Komu warto podziękować?', 1],
];
const przeglad = `<div class="str">
<div class="gl"><img src="${logo}"><div><b>Guliwera 4</b><span>Pakiet startowy Kierunek Zysk</span></div></div>
<p class="pis" style="margin-top:6mm">ostatni piątek miesiąca</p>
<h1>Przegląd miesiąca w 15 minut</h1>
<p class="lead">7 pytań, kartka i długopis. Odpowiadaj krótko: tu liczy się regularność, nie elegancja. Zachowuj kartki: po kwartale zobaczysz wzory, których nie widać z bliska.</p>
<div class="data">Miesiąc: ______________________ &nbsp;&nbsp; Czas: start ____ : ____ &nbsp; koniec ____ : ____</div>
${pyt.map(([t, p, n], i) => `<div class="pyt"><div class="nr">${i + 1}</div><div class="tr"><h2>${t}</h2><p>${p}</p>${'<div class="lin"></div>'.repeat(n)}</div></div>`).join('')}
<div class="stopka"><span>Liczby do punktu 1 policzysz w <b>Kalkulatorze zysku</b> z Pakietu startowego. Plan na kwartał: <b>Planer 90 dni</b>, guliwera4.pl/materialy</span><span>Guliwera 4 · kontakt@guliwera4.pl</span></div>
</div>
<style>
.data{font-size:11px;color:#7A6580;margin:-2mm 0 4mm;position:relative}
.pyt{display:flex;gap:4mm;background:#fff;border-radius:12px;padding:2.4mm 4.5mm 2.8mm;margin-bottom:2.2mm;position:relative}
.nr{flex:none;width:9mm;height:9mm;border-radius:50%;background:#FFD8C2;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:13px}
.pyt:nth-child(odd) .nr{background:#EDE5FB}
.tr{flex:1}.tr h2{font-family:Fraunces,Georgia,serif;font-size:15px;margin:0}.tr p{margin:.5mm 0 1mm;font-size:11px;color:#7A6580}
.lin{border-bottom:1px solid #E5D3C5;height:5.6mm}
</style>`;

(async () => {
  fs.mkdirSync(out, { recursive: true });
  const b = await chromium.launch();
  const p = await b.newPage();
  for (const [nazwa, tresc] of [['strategia-na-1-stronie-przyklad', strategia], ['przeglad-miesiaca-15-minut', przeglad]]) {
    await p.setContent(`<!doctype html><html lang="pl"><head><meta charset="utf-8"><style>${css}</style></head><body>${tresc}</body></html>`, { waitUntil: 'networkidle' });
    await p.evaluate(() => document.fonts.ready);
    await p.pdf({ path: path.join(out, nazwa + '.pdf'), format: 'A4', printBackground: true, preferCSSPageSize: true });
    await p.setViewportSize({ width: 794, height: 1123 });
    await p.screenshot({ path: path.join(out, nazwa + '.png') });
  }
  await b.close();
  console.log('ok');
})();
