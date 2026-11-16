// Wafel: owczarek australijski blue merle tricolor, maskotka Guliwera 4.
// Wzorowany na prawdziwym Waflu: szeroka biała strzałka lekko w lewo, lewa (dla patrzącego) strona pyska
// marmurkowa z brązowym okiem, prawa czarna maska z niebieskim okiem, miedziane brwi i policzki,
// duży ciemny nos, miękkie załamane uszy, żółta obroża.
//   node wafel/generuj-wafla.js            -> SVG w 4 nastrojach
//   node wafel/generuj-wafla.js --png plik -> dodatkowo podgląd PNG
const fs = require('fs'), path = require('path');
const C = {
  czarny: '#1E2A4A',  // czerń sierści = granat marki
  cien: '#141C33',
  merle: '#A9B0C2',   // niebieskoszary marmurek
  merleJ: '#C9CEDB',
  bialy: '#FFFFFF',
  krem: '#F3E9E1',
  miedz: '#D9773F',
  nos: '#3A2F35',
  brazOko: '#8A5426',
  niebOko: '#8CC8F2',
  roz: '#F4799F',
  rozC: '#D85C83',
  koral: '#FF6B3D',
  zolty: '#FFC84A',
  zoltyC: '#E9A92A',
};

function oko(cx, cy, kolor, nastroj, strona) {
  if ((nastroj === 'mruga' && strona === 'p') || nastroj === 'spi') {
    const d = nastroj === 'spi' ? `M${cx - 12} ${cy} Q${cx} ${cy + 8} ${cx + 12} ${cy}` : `M${cx - 13} ${cy + 2} Q${cx} ${cy - 9} ${cx + 13} ${cy + 2}`;
    return `<path d="${d}" fill="none" stroke="${C.cien}" stroke-width="4.5" stroke-linecap="round"/>`;
  }
  const r = 13.5;
  // lekko przymrużona górna powieka nadaje charakter (patrz zdjęcia)
  const powieka = nastroj === 'dumny'
    ? `<path d="M${cx - r - 3} ${cy - 4} Q${cx} ${cy - r - 6} ${cx + r + 3} ${cy - 4} L${cx + r + 3} ${cy - r - 6} L${cx - r - 3} ${cy - r - 6}Z" fill="${strona === 'l' ? C.merle : C.czarny}"/>`
    : '';
  return `<g>
    <ellipse cx="${cx}" cy="${cy}" rx="${r + 3}" ry="${r + 2}" fill="${C.cien}"/>
    <circle cx="${cx}" cy="${cy}" r="${r}" fill="${kolor}"/>
    <circle cx="${cx}" cy="${cy}" r="${r * 0.62}" fill="none" stroke="${C.cien}" stroke-opacity=".18" stroke-width="2"/>
    <circle cx="${cx}" cy="${cy + 1}" r="${r * 0.42}" fill="${C.cien}"/>
    <circle cx="${cx + 4.5}" cy="${cy - 5}" r="3.8" fill="#fff"/>
    <circle cx="${cx - 4}" cy="${cy + 5}" r="1.6" fill="#fff" opacity=".8"/>
    ${powieka}
  </g>`;
}

const GLOWA = `M200 98
    C248 98 280 122 288 166
    C298 180 306 198 310 222
    C300 218 294 224 292 234
    C282 262 248 300 200 302
    C152 300 118 262 108 234
    C106 224 100 218 90 222
    C94 198 102 180 112 166
    C120 122 152 98 200 98Z`;

function wafel(nastroj = 'radosny', { obroza = true } = {}) {
  const tilt = { radosny: -6, mruga: 7, dumny: 0, spi: -10 }[nastroj];
  const id = 'w' + nastroj;
  const otwarte = nastroj === 'radosny' || nastroj === 'mruga';
  const pysk = otwarte
    ? `<path d="M178 256 Q200 266 222 256 Q220 276 200 280 Q180 276 178 256Z" fill="${C.nos}"/>
       <path d="M184 266 Q200 272 216 266 Q217 296 200 302 Q183 296 184 266Z" fill="${C.roz}"/>
       <path d="M200 272 L200 294" stroke="${C.rozC}" stroke-width="2.5" stroke-linecap="round"/>
       <path d="M200 246 Q200 258 178 256 M200 246 Q200 258 222 256" fill="none" stroke="${C.nos}" stroke-width="3.5" stroke-linecap="round"/>`
    : `<path d="M200 244 Q200 258 182 258 M200 244 Q200 258 218 258" fill="none" stroke="${C.nos}" stroke-width="3.5" stroke-linecap="round"/>`;

  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
  <defs><clipPath id="${id}g"><path d="${GLOWA}"/></clipPath></defs>
  <!-- tułów: czarno-marmurkowe boki, biała kryza -->
  <path d="M84 400 C90 330 128 290 200 288 C272 290 310 330 316 400Z" fill="${C.czarny}"/>
  <path d="M84 400 C88 352 104 322 132 306 C126 340 128 372 136 400Z" fill="${C.merle}"/>
  <circle cx="104" cy="370" r="7" fill="${C.czarny}"/><circle cx="118" cy="338" r="5" fill="${C.czarny}"/>
  <path d="M128 400 C128 350 156 300 200 296 C244 300 272 350 272 400Z" fill="${C.bialy}"/>
  <path d="M150 400 q9 -16 18 0 q8 -20 16 0 q8 -22 16 0 q8 -20 16 0 q9 -16 18 0Z" fill="${C.krem}"/>
  ${obroza ? `<!-- żółta obroża z koralową zawieszką -->
  <path d="M140 300 Q200 326 260 300 L262 314 Q200 342 138 314Z" fill="${C.zolty}"/>
  <path d="M140 300 Q200 326 260 300 L261 305 Q200 331 139 305Z" fill="${C.zoltyC}" opacity=".6"/>
  <circle cx="200" cy="334" r="4" fill="none" stroke="${C.zoltyC}" stroke-width="2.5"/>
  <circle cx="200" cy="350" r="12" fill="${C.koral}"/>
  <text x="200" y="355" text-anchor="middle" font-family="Fraunces, Georgia, serif" font-weight="700" font-size="14" fill="#fff">4</text>` : ''}

  <g transform="rotate(${tilt} 200 210)">
  <!-- uszy: miękkie, załamane do przodu; lewe marmurkowe, prawe czarne -->
  <path d="M114 160 C100 128 104 96 128 86 C150 80 172 96 184 112Z" fill="${C.merle}"/>
  <path d="M128 86 C112 92 96 112 92 140 C108 128 130 116 150 108 C146 96 138 88 128 86Z" fill="${C.czarny}"/>
  <circle cx="112" cy="128" r="5" fill="${C.merleJ}"/>
  <path d="M286 160 C300 128 296 96 272 86 C250 80 228 96 216 112Z" fill="${C.czarny}"/>
  <path d="M272 86 C288 92 304 112 308 140 C292 128 270 116 250 108 C254 96 262 88 272 86Z" fill="${C.cien}"/>

  <!-- głowa: baza czarna, lewa strona marmurkowa -->
  <path d="${GLOWA}" fill="${C.czarny}"/>
  <g clip-path="url(#${id}g)">
    <path d="M80 90 L196 90 C188 140 176 170 168 210 L80 260Z" fill="${C.merle}"/>
    <!-- plamy merle -->
    <ellipse cx="128" cy="150" rx="16" ry="11" fill="${C.czarny}" transform="rotate(-20 128 150)"/>
    <ellipse cx="160" cy="118" rx="12" ry="8" fill="${C.czarny}"/>
    <ellipse cx="112" cy="196" rx="9" ry="13" fill="${C.czarny}"/>
    <circle cx="142" cy="176" r="6" fill="${C.czarny}"/>
    <circle cx="176" cy="150" r="4" fill="${C.czarny}"/>
    <circle cx="118" cy="174" r="3.5" fill="${C.merleJ}"/><circle cx="150" cy="134" r="3" fill="${C.merleJ}"/><circle cx="104" cy="214" r="3" fill="${C.merleJ}"/>
    <!-- miedziane policzki -->
    <path d="M100 206 C106 244 132 276 168 288 C152 262 142 236 140 212Z" fill="${C.miedz}"/>
    <path d="M300 206 C294 244 268 276 232 288 C248 262 258 236 260 212Z" fill="${C.miedz}"/>
    <circle cx="124" cy="240" r="4" fill="${C.czarny}" opacity=".7"/><circle cx="134" cy="256" r="3" fill="${C.czarny}" opacity=".6"/>
  </g>

  <!-- szeroka biała strzałka (lekko w lewo) przechodząca w biały pysk -->
  <path d="M176 98 C186 96 202 96 210 98
    C208 128 206 150 212 172
    C226 192 252 206 262 230
    C268 262 240 290 200 292
    C160 290 132 262 138 230
    C148 206 168 192 176 172
    C174 146 172 122 176 98Z" fill="${C.bialy}"/>
  <circle cx="186" cy="132" r="2.4" fill="${C.merle}"/><circle cx="196" cy="160" r="2" fill="${C.merle}"/>

  <!-- miedziane brwi -->
  <ellipse cx="152" cy="158" rx="11" ry="7.5" fill="${C.miedz}" transform="rotate(-16 152 158)"/>
  <ellipse cx="246" cy="158" rx="11" ry="7.5" fill="${C.miedz}" transform="rotate(16 246 158)"/>

  <!-- oczy: brązowe po marmurkowej stronie, niebieskie w czarnej masce -->
  ${oko(156, 188, C.brazOko, nastroj, 'l')}
  ${oko(244, 188, C.niebOko, nastroj, 'p')}

  <!-- duży ciemny nos -->
  <path d="M176 222 Q200 210 224 222 Q228 238 200 248 Q172 238 176 222Z" fill="${C.nos}"/>
  <ellipse cx="190" cy="222" rx="7" ry="3" fill="#fff" opacity=".28"/>
  ${pysk}
  </g>
  ${nastroj === 'spi' ? `<text x="270" y="110" font-family="Caveat, cursive" font-weight="700" font-size="36" fill="${C.koral}">z z z</text>` : ''}
</svg>`;
}

module.exports = { wafel };
const NASTROJE = ['radosny', 'mruga', 'dumny', 'spi'];
if (require.main === module) {
  for (const n of NASTROJE) fs.writeFileSync(path.join(__dirname, `wafel-${n}.svg`), wafel(n));
  const i = process.argv.indexOf('--png');
  if (i > 0) {
    const { chromium } = require('playwright');
    (async () => {
      const b = await chromium.launch();
      const p = await b.newPage({ viewport: { width: 1680, height: 440 } });
      await p.setContent(`<body style="margin:0;background:#FFF6EE;display:flex;gap:20px;padding:20px">${NASTROJE.map(n => wafel(n)).join('')}</body>`);
      await p.screenshot({ path: process.argv[i + 1] });
      await b.close();
    })();
  }
}
