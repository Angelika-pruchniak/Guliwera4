// Planer roczny Q1–Q4 · Guliwera 4 · Kierunek Zysk
// Podgląd i pliki do druku: okładka (przód, tył) + przykładowe strony środka.
// Format A5 148 × 210 mm + 3 mm spadu (154 × 216 mm). Uruchom: node generuj.js KATALOG_WYJSCIOWY
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');
const OUT = process.argv[2] || '.';

const mark = fs.readFileSync(path.join(__dirname, '../../logo/mark.svg'), 'utf8')
  .replace('<svg ', '<svg class="mark" ');

const Q = {
  1: { k: '#FF6B3D', j: '#FFD8C2', n: 'Kierunek', o: 'Strategia: gdzie gram i jak wygrywam' },
  2: { k: '#FFC84A', j: '#FFEFC7', n: 'Liczby', o: 'Ekonomia firmy: ile naprawdę zarabiam' },
  3: { k: '#B9A5E3', j: '#EDE5FB', n: 'Problemy', o: 'Myśl jak konsultant: rozkładaj, szukaj 20%' },
  4: { k: '#F4799F', j: '#FFD9E4', n: 'Decyzje', o: 'Ekonomia decyzji: co kończę, w co inwestuję' },
};

const css = `
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Caveat:wght@600&display=swap');
@page{size:154mm 216mm;margin:0}
:root{--krem:#FFF6EE;--koral:#FF6B3D;--koral-c:#E8512A;--roz:#F4799F;--roz-j:#FFD9E4;--lil:#B9A5E3;--lil-j:#EDE5FB;
--zol:#FFC84A;--zol-j:#FFEFC7;--brzo:#FFD8C2;--atr:#3A2340;--atr6:#7A6580;--gran:#1E2A4A}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:"DM Sans",sans-serif;color:var(--atr);-webkit-print-color-adjust:exact;print-color-adjust:exact}
.p{width:154mm;height:216mm;position:relative;overflow:hidden;background:#fff;page-break-after:always}
/* obszar bezpieczny: 3 mm spad + 10 mm margines */
.in{position:absolute;inset:13mm 14mm 13mm 14mm}
.mark{width:100%;height:100%}
h1,h2,h3{font-family:"Fraunces",serif;font-weight:700;letter-spacing:-.01em;line-height:1.05}
.hand{font-family:"Caveat",cursive;font-weight:600;color:var(--koral)}
.pill{display:inline-block;border-radius:999px;font-weight:700;font-size:7pt;letter-spacing:.12em;text-transform:uppercase;padding:1.3mm 3.2mm}
.swoosh{background:linear-gradient(transparent 62%,var(--zol) 62%,var(--zol) 88%,transparent 88%);padding:0 .5mm}
.tab{position:absolute;right:0;width:8mm;height:44mm}
.foot{position:absolute;left:14mm;right:14mm;bottom:9mm;display:flex;justify-content:space-between;font-size:6.5pt;color:var(--atr6)}
.dots{background-image:radial-gradient(circle,#CDBFD0 .28mm,transparent .3mm);background-size:5mm 5mm}
.box{border:.3mm solid #E3D6E0;border-radius:2.5mm;padding:2.5mm 3mm}
.lbl{font-size:6.5pt;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--atr6)}
.cb{display:inline-block;width:3mm;height:3mm;border:.35mm solid var(--atr6);border-radius:.7mm;vertical-align:-.5mm;margin-right:1.5mm}
.dot{display:inline-block;width:2.4mm;height:2.4mm;border-radius:50%;margin-right:1.4mm;vertical-align:-.2mm}
.line{border-bottom:.25mm solid #E3D6E0;height:6mm}
`;

const tab = (q, i) => `<div class="tab" style="top:${16 + (q - 1) * 46}mm;background:${Q[q].k}"></div>`;
const foot = (l, r) => `<div class="foot"><span>${l}</span><span>${r}</span></div>`;

// ---------- OKŁADKA: PRZÓD ----------
const okladka = `
<div class="p" style="background:var(--krem)">
  <div style="position:absolute;width:150mm;height:150mm;border-radius:50%;background:var(--roz-j);right:-70mm;top:-55mm"></div>
  <div style="position:absolute;width:80mm;height:80mm;border-radius:50%;background:var(--lil-j);left:-30mm;bottom:-28mm"></div>
  <div style="position:absolute;width:44mm;height:44mm;border-radius:50%;background:var(--zol-j);right:16mm;bottom:40mm"></div>
  <div class="in" style="display:flex;flex-direction:column">
    <div style="display:flex;align-items:center;gap:3mm">
      <div style="width:13mm;height:13mm">${mark}</div>
      <div><div style="font-family:Fraunces;font-weight:700;font-size:15pt;line-height:1">Guliwera <span style="color:var(--koral)">4</span></div>
      <div style="font-size:6.5pt;color:var(--atr6);margin-top:.8mm">Angelika Pruchniak-Gąszczyk · strategia i produkty cyfrowe</div></div>
    </div>
    <div style="margin-top:34mm">
      <span class="pill" style="background:var(--gran);color:#fff">Kierunek Zysk · planer roczny</span>
      <div class="hand" style="font-size:15pt;margin-top:5mm">strategia, produkty cyfrowe i dobry humor</div>
      <h1 style="font-size:35pt;margin-top:2mm">Rok w&nbsp;czterech <span class="swoosh">kwartałach</span></h1>
      <div style="font-size:9.5pt;line-height:1.45;margin-top:5mm;max-width:100mm">
        16 lekcji z&nbsp;konsultingu strategicznego i&nbsp;ekonomii. Plan kwartału, tygodnia i&nbsp;dnia
        dla głów, które mają otwartych 20 kart naraz.</div>
    </div>
    <div style="margin-top:auto;display:flex;gap:2mm">
      ${[1, 2, 3, 4].map(q => `<span class="pill" style="background:${Q[q].k};color:${q === 2 ? 'var(--atr)' : '#fff'}">Q${q} · ${Q[q].n}</span>`).join('')}
    </div>
    <div style="display:flex;justify-content:space-between;border-top:.3mm solid rgba(58,35,64,.2);padding-top:2.5mm;margin-top:4mm;font-size:7pt;color:var(--atr6)">
      <span>guliwera4.pl</span><span>planer niedatowany · zacznij w dowolnym miesiącu</span></div>
  </div>
</div>`;

// ---------- OKŁADKA: TYŁ ----------
const tyl = `
<div class="p" style="background:var(--gran);color:#fff">
  <div style="position:absolute;width:120mm;height:120mm;border-radius:50%;background:rgba(255,107,61,.13);left:-50mm;top:-40mm"></div>
  <div class="in" style="display:flex;flex-direction:column">
    <div class="hand" style="font-size:14pt;color:var(--zol)">co jest w środku</div>
    <h2 style="font-size:19pt;margin-top:1mm">Bank wiedzy, z&nbsp;którego korzystają stratedzy i&nbsp;ekonomiści, w&nbsp;formie, która da się zrobić w&nbsp;10 minut</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3mm;margin-top:7mm;font-size:7.6pt;line-height:1.4">
      ${[1, 2, 3, 4].map(q => `<div style="border-left:1.2mm solid ${Q[q].k};padding-left:2.5mm"><b>Q${q} · ${Q[q].n}</b><br>${Q[q].o}</div>`).join('')}
    </div>
    <div style="font-size:7.6pt;line-height:1.5;margin-top:6mm;opacity:.92">
      MECE · drzewo problemów · piramida Minto · zasada 80/20 · SWOT i 5 sił · macierz BCG · model 7S · OKR ·
      próg rentowności · CAC i LTV · elastyczność cenowa · cash flow · koszt alternatywny · koszt utopiony ·
      analiza krańcowa · ROI i NPV · słownik 40 pojęć biznesu
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:2.5mm;margin-top:8mm;font-size:7.3pt;line-height:1.4">
      ${[['16','lekcji: wiedza + przykład z firmy + ćwiczenie na 10 minut'],['52','tygodnie z Top 3, „zrzutem z głowy” i listą „nie teraz”'],['4','przeglądy kwartału jak w zarządzie: liczby, wnioski, decyzja']]
        .map(([n,t])=>`<div style="background:rgba(255,255,255,.07);border-radius:2.5mm;padding:3mm"><div style="font-family:Fraunces;font-weight:700;font-size:20pt;color:var(--zol);line-height:1">${n}</div><div style="margin-top:1.5mm">${t}</div></div>`).join('')}
    </div>
    <div style="margin-top:8mm;border-left:1.2mm solid var(--koral);padding-left:3.5mm">
      <div style="font-family:Fraunces;font-size:12pt;line-height:1.3">„Strategia to nie dokument na półkę. To decyzja, co robisz w&nbsp;ten poniedziałek – i&nbsp;czego nie robisz.”</div>
      <div class="hand" style="font-size:11pt;color:var(--zol);margin-top:1.5mm">Angelika</div>
    </div>
    <div style="margin-top:auto;display:flex;gap:4mm;align-items:flex-end">
      <div style="flex:1;font-size:7.4pt;line-height:1.45">
        <b style="font-family:Fraunces;font-size:10pt">Angelika Pruchniak-Gąszczyk</b><br>
        Strateżka. Zamienia duże strategie w&nbsp;konkretną trasę. Autorka materiałów „Kierunek Zysk” i&nbsp;Akademii Guliwera.
      </div>
      <div style="width:12mm;height:12mm">${mark}</div>
    </div>
    <div style="display:flex;justify-content:space-between;border-top:.3mm solid rgba(255,255,255,.25);padding-top:2.5mm;margin-top:4mm;font-size:6.3pt;opacity:.85">
      <span>Producent: Guliwera 4, ul. Guliwera 4, Lublin · kontakt@guliwera4.pl</span><span>Wydrukowano w Polsce · partia 2026-12</span></div>
  </div>
</div>`;

// ---------- OTWARCIE KWARTAŁU (Q1) ----------
const otwarcie = `
<div class="p" style="background:${Q[1].j}">
  <div style="position:absolute;width:130mm;height:130mm;border-radius:50%;background:rgba(255,255,255,.45);right:-50mm;top:-30mm"></div>
  ${tab(1)}
  <div class="in" style="display:flex;flex-direction:column">
    <span class="pill" style="background:var(--gran);color:#fff;align-self:flex-start">Kwartał 1 z 4</span>
    <div style="font-family:Fraunces;font-weight:700;font-size:90pt;line-height:.9;color:${Q[1].k};margin-top:16mm">Q1</div>
    <h1 style="font-size:28pt;margin-top:2mm">Kierunek</h1>
    <div class="hand" style="font-size:14pt;margin-top:2mm">najpierw mapa, potem gaz</div>
    <div style="margin-top:9mm;font-size:8.6pt;line-height:1.5;max-width:100mm">
      W tym kwartale wybierasz, <b>gdzie grasz i jak wygrywasz</b>. Cztery narzędzia, z których korzystają zespoły strategii
      w dużych firmach, w wersji na jedną kartkę:</div>
    <div style="margin-top:5mm;display:grid;gap:2.2mm;font-size:8.4pt">
      ${['SWOT i 5 sił – gdzie jest moja przewaga', 'Macierz BCG – które projekty karmić, a które zamknąć', 'Strategia na 1 stronie – wniosek na górze (piramida Minto)', 'OKR – 1 cel i 3 mierzalne rezultaty na 13 tygodni']
        .map((t, i) => `<div style="display:flex;gap:2.5mm;align-items:center"><span style="width:6mm;height:6mm;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:7.5pt">${i + 1}</span>${t}</div>`).join('')}
    </div>
    <div class="box" style="margin-top:auto;background:rgba(255,255,255,.6);border:0;font-size:8pt;line-height:1.7">
      <span class="lbl">Po 13 tygodniach masz</span><br>
      <span class="cb"></span>mapę przewagi i ryzyk na 1 stronie<br>
      <span class="cb"></span>listę projektów do rozwoju i do zamknięcia<br>
      <span class="cb"></span>1 cel, 3 mierzalne rezultaty i plan tygodni</div>
  </div>
  ${foot('Planer roczny · Kierunek Zysk', 'guliwera4.pl')}
</div>`;

// ---------- LEKCJA (rozkładówka: wiedza + ćwiczenie) ----------
const lekcjaL = `
<div class="p">
  <div class="in">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <span class="pill" style="background:${Q[1].j};color:var(--atr)">Q1 · Lekcja 2 z 16</span>
      <span class="lbl">Narzędzie strategów · 5 min</span></div>
    <h2 style="font-size:22pt;margin-top:6mm">Macierz BCG: <span class="swoosh">co karmić</span>, co zamknąć</h2>
    <div class="hand" style="font-size:12pt;margin-top:1.5mm">nie wszystko, co robisz, zasługuje na Twój czas</div>
    <p style="font-size:8.4pt;line-height:1.55;margin-top:4mm">
      Macierz opracowana w&nbsp;Boston Consulting Group dzieli produkty (albo projekty, klientów, usługi) według dwóch osi:
      <b>tempa wzrostu rynku</b> i&nbsp;<b>Twojej pozycji</b> na tym rynku. Wynik to cztery decyzje, nie cztery etykiety.</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2mm;margin-top:5mm;font-size:7.6pt;line-height:1.4">
      <div class="box" style="background:var(--zol-j);border:0"><b><i class="dot" style="background:#FFC84A"></i>Gwiazdy</b><br>rosnący rynek, mocna pozycja. <i>Inwestuj.</i></div>
      <div class="box" style="background:var(--lil-j);border:0"><b><i class="dot" style="background:#B9A5E3"></i>Znaki zapytania</b><br>rosnący rynek, słaba pozycja. <i>Testuj albo odpuść.</i></div>
      <div class="box" style="background:var(--brzo);border:0"><b><i class="dot" style="background:#FF6B3D"></i>Dojne krowy</b><br>wolny rynek, mocna pozycja. <i>Utrzymuj, finansuj resztę.</i></div>
      <div class="box" style="background:var(--roz-j);border:0"><b><i class="dot" style="background:#F4799F"></i>Psy</b><br>wolny rynek, słaba pozycja. <i>Zamknij lub uprość.</i></div>
    </div>
    <div class="box" style="margin-top:5mm;font-size:7.8pt;line-height:1.5">
      <span class="lbl">Przykład z małej firmy</span><br>
      Konsultacje 1:1 to dojna krowa (stały przychód), kurs online – znak zapytania, newsletter drukowany – pies.
      Decyzja: z&nbsp;zysku z&nbsp;konsultacji finansuję 3 testy kursu, newsletter drukowany zamykam w&nbsp;Q2.</div>
    <div style="margin-top:5mm;font-size:7.8pt;line-height:1.5;border-left:1.2mm solid ${Q[1].k};padding-left:3mm">
      <b>Dla rozproszonej głowy:</b> nie oceniaj wszystkiego naraz. Wpisz tylko 5 rzeczy, które zabierają Ci najwięcej czasu.</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2mm;margin-top:5mm;font-size:7.6pt;line-height:1.45">
      <div class="box" style="background:var(--krem);border:0"><span class="lbl">Pytanie konsultanta</span><br>„Gdyby to był nowy pomysł, czy wszedłby dziś do planu?” Jeśli nie – <b>to kandydat do zamknięcia</b>.</div>
      <div class="box" style="background:var(--krem);border:0"><span class="lbl">Typowy błąd</span><br>Trzymanie „psa” z&nbsp;sentymentu. Pieniądze już wydane to <b>koszt utopiony</b> (lekcja 14) – nie wracają.</div>
    </div>
  </div>
  ${foot('Więcej: Akademia Guliwera · lekcja „Macierz BCG”', '24')}
</div>`;

const lekcjaP = `
<div class="p">
  ${tab(1)}
  <div class="in">
    <div class="lbl">Ćwiczenie · 10 minut</div>
    <h3 style="font-size:14pt;margin-top:2mm">Moja macierz na ten kwartał</h3>
    <div style="position:relative;margin-top:5mm;height:92mm;display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:1.5mm;padding-left:6mm;padding-bottom:6mm">
      ${['<i class="dot" style="background:#FFC84A"></i>Gwiazdy', '<i class="dot" style="background:#B9A5E3"></i>Znaki zapytania', '<i class="dot" style="background:#FF6B3D"></i>Dojne krowy', '<i class="dot" style="background:#F4799F"></i>Psy'].map((t, i) => `<div class="box dots" style="background-color:${['#FFEFC7', '#EDE5FB', '#FFD8C2', '#FFD9E4'][i]}66;border:0"><span class="lbl">${t}</span></div>`).join('')}
      <div style="position:absolute;left:0;top:0;bottom:6mm;writing-mode:vertical-rl;transform:rotate(180deg);font-size:6.5pt;color:var(--atr6);text-align:center">wzrost rynku →</div>
      <div style="position:absolute;left:6mm;right:0;bottom:0;font-size:6.5pt;color:var(--atr6);text-align:center">← moja pozycja (mocna · słaba)</div>
    </div>
    <div style="margin-top:4mm;font-size:8pt"><b>Jedna decyzja na ten kwartał</b></div>
    <div class="line"></div><div class="line"></div>
    <div style="margin-top:4mm;font-size:8pt"><b>Co przestaję robić</b> <span style="color:var(--atr6)">(koszt alternatywny: na co zwolni się czas?)</span></div>
    <div class="line"></div><div class="line"></div>
    <div style="margin-top:4mm;font-size:7.8pt"><span class="cb"></span>Wpisane do planu kwartału (s. 30)</div>
  </div>
  ${foot('Planer roczny · Kierunek Zysk', '25')}
</div>`;

// ---------- TYDZIEŃ (rozkładówka) ----------
const dni = ['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob / Nd'];
const tydzL = `
<div class="p">
  <div class="in">
    <div style="display:flex;justify-content:space-between;align-items:baseline">
      <h3 style="font-size:15pt">Tydzień <span style="display:inline-block;width:12mm;border-bottom:.3mm solid var(--atr6)"></span> z 13</h3>
      <span style="font-size:7pt;color:var(--atr6)">od ____ . ____ do ____ . ____</span></div>
    <div class="box" style="margin-top:3.5mm;background:${Q[1].j};border:0">
      <span class="lbl">Top 3 tygodnia · zasada 80/20: co da 80% efektu?</span>
      ${[1, 2, 3].map(i => `<div style="display:flex;align-items:center;font-size:8pt;margin-top:1.8mm"><span class="cb"></span><div style="flex:1;border-bottom:.25mm solid rgba(58,35,64,.25);height:4.5mm"></div></div>`).join('')}
    </div>
    <div style="margin-top:3mm;display:grid;gap:1.6mm">
      ${dni.map(d => `<div style="display:grid;grid-template-columns:13mm 1fr;gap:2mm;border-top:.25mm solid #E3D6E0;padding-top:1.2mm;height:20.6mm">
        <div style="font-weight:700;font-size:7.5pt">${d}</div>
        <div style="font-size:6.5pt;color:var(--atr6)"><span style="color:var(--koral);font-weight:700">★ 1 najważniejsza rzecz</span>
          <div style="border-bottom:.25mm solid #E3D6E0;height:4.5mm"></div>
          <div style="margin-top:1.3mm"><span class="cb"></span><span class="cb" style="margin-left:22mm"></span><span class="cb" style="margin-left:22mm"></span></div></div></div>`).join('')}
    </div>
  </div>
  ${foot('Planer roczny · Kierunek Zysk', '32')}
</div>`;

const tydzP = `
<div class="p">
  ${tab(1)}
  <div class="in" style="display:flex;flex-direction:column;gap:3mm">
    <div class="box dots" style="height:58mm"><span class="lbl">Zrzut z głowy · wszystko, co krąży</span></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3mm">
      <div class="box" style="height:34mm;background:var(--lil-j);border:0"><span class="lbl">Nie teraz · parking</span></div>
      <div class="box" style="height:34mm"><span class="lbl">Liczba tygodnia (KPI)</span>
        <div style="font-family:Fraunces;font-size:22pt;color:#E3D6E0;margin-top:4mm">____</div>
        <div style="font-size:6.5pt;color:var(--atr6)">vs cel kwartału: ______</div></div>
    </div>
    <div class="box"><span class="lbl">Energia tygodnia</span>
      <div style="display:flex;gap:3mm;margin-top:2mm">${[1, 2, 3, 4, 5].map(n => `<span style="width:7mm;height:7mm;border-radius:50%;border:.35mm solid var(--atr6);display:flex;align-items:center;justify-content:center;font-size:7pt">${n}</span>`).join('')}</div></div>
    <div class="box" style="height:30mm;background:var(--zol-j);border:0"><span class="lbl">Wygrane tygodnia · nawet małe</span></div>
    <div class="box"><span class="lbl">Decyzja jak ekonomista</span>
      <div style="font-size:7.2pt;margin-top:1.5mm;line-height:1.5">Czego <b>nie</b> zrobię, żeby zrobić Top 3? (koszt alternatywny)</div>
      <div class="line"></div></div>
  </div>
  ${foot('Planer roczny · Kierunek Zysk', '33')}
</div>`;

const strony = { okladka, tyl, otwarcie, lekcjaL, lekcjaP, tydzL, tydzP };

(async () => {
  const b = await chromium.launch();
  const pg = await b.newPage({ viewport: { width: 582, height: 816 }, deviceScaleFactor: 2 });
  const html = body => `<!doctype html><html lang="pl"><head><meta charset="utf-8"><style>${css}</style></head><body>${body}</body></html>`;
  // wszystkie strony w jednym PDF (kolejność podglądu)
  await pg.setContent(html(Object.values(strony).join('')), { waitUntil: 'networkidle' });
  await pg.evaluate(() => document.fonts.ready);
  await pg.pdf({ path: path.join(OUT, 'planer-podglad.pdf'), width: '154mm', height: '216mm', printBackground: true });
  // PNG każdej strony (podgląd)
  for (const [n, s] of Object.entries(strony)) {
    await pg.setContent(html(s), { waitUntil: 'networkidle' });
    await pg.evaluate(() => document.fonts.ready);
    await pg.screenshot({ path: path.join(OUT, `${n}.png`) });
  }
  await b.close();
  console.log('OK', OUT);
})();
