// Wafel: oficjalna maskotka Guliwera 4 (zastępuje Lilka we wszystkich materiałach).
// Owczarek australijski: liliowy merle + granat, biała strzałka na głowie, brązowe oko po liliowej stronie,
// niebieskie w granatowej masce, koralowe policzki, koralowa bandana z „4”, puszysta biała pierś, białe łapy.
//
//   const { wafel } = require('brand/wafel/generuj-wafla.js');  wafel('siedzi') -> SVG
//   node brand/wafel/generuj-wafla.js [--png]   -> svg/*.svg (+ png/*.png 800 px)
//
// Pozy: siedzi, macha, mysli, radosc, mruga, smutny, spi, glowa (awatar w żółtym kole).
const fs = require('fs'), path = require('path');

const C = {
  granat: '#1E2A4A', granatJ: '#34446E', lil: '#B9A5E3', lilC: '#9C86D2', lilJ: '#D9CCF3',
  bialy: '#FFFFFF', krem: '#F4ECE6', koral: '#FF6B3D', koralC: '#E8512A', zolty: '#FFC84A', zoltyJ: '#FFEFC7',
  atrament: '#3A2340', braz: '#9A5B22', niebieski: '#59AEEA', roz: '#F4799F', cien: '#EAD9CE',
};

// ---------- głowa (środek ~200,160) ----------
const GLOWA = 'M200 70 C258 70 294 110 296 160 C304 176 302 196 290 208 C282 238 248 258 200 260 C152 258 118 238 110 208 C98 196 96 176 104 160 C106 110 142 70 200 70Z';

function oko(cx, cy, kolor, typ, kreska = C.atrament) {
  if (typ === 'zamkniete') return `<path d="M${cx - 14} ${cy + 2} Q${cx} ${cy - 10} ${cx + 14} ${cy + 2}" fill="none" stroke="${kreska}" stroke-width="5" stroke-linecap="round"/>`;
  if (typ === 'spi') return `<path d="M${cx - 13} ${cy - 1} Q${cx} ${cy + 9} ${cx + 13} ${cy - 1}" fill="none" stroke="${kreska}" stroke-width="4.5" stroke-linecap="round"/>`;
  const dy = typ === 'gora' ? -5 : 0;
  return `<g>
    <ellipse cx="${cx}" cy="${cy}" rx="16" ry="18" fill="${C.atrament}"/>
    <ellipse cx="${cx}" cy="${cy + 1}" rx="13" ry="15" fill="${kolor}"/>
    <circle cx="${cx}" cy="${cy + 2 + dy}" r="7" fill="${C.atrament}"/>
    <circle cx="${cx + 5}" cy="${cy - 6 + dy}" r="4.6" fill="#fff"/>
    <circle cx="${cx - 5}" cy="${cy + 7 + dy}" r="2" fill="#fff" opacity=".85"/>
  </g>`;
}

function glowa(p) {
  const { oczy = ['otwarte', 'otwarte'], usta = 'usmiech', brwi = 'zwykle', id } = p;
  const brewL = brwi === 'smutne' ? 'M136 120 Q150 110 166 118' : brwi === 'gora' ? 'M138 112 Q152 100 168 108' : 'M138 120 Q152 108 168 114';
  const brewP = brwi === 'smutne' ? 'M234 118 Q250 110 264 120' : brwi === 'gora' ? 'M232 108 Q248 100 262 112' : 'M232 114 Q248 108 262 120';
  const pysk = {
    usmiech: `<path d="M200 206 L200 216 M172 214 Q186 230 200 216 Q214 230 228 214" fill="none" stroke="${C.atrament}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>`,
    otwarte: `<path d="M200 206 L200 214" stroke="${C.atrament}" stroke-width="4.5" stroke-linecap="round"/>
      <path d="M172 214 Q200 222 228 214 Q226 246 200 250 Q174 246 172 214Z" fill="${C.atrament}"/>
      <path d="M182 234 Q200 228 218 234 Q214 248 200 250 Q186 248 182 234Z" fill="${C.koral}"/>`,
    plaskie: `<path d="M200 206 L200 216 M186 222 Q200 216 214 222" fill="none" stroke="${C.atrament}" stroke-width="4.5" stroke-linecap="round"/>`,
    smutne: `<path d="M200 206 L200 216 M182 228 Q200 214 218 228" fill="none" stroke="${C.atrament}" stroke-width="4.5" stroke-linecap="round"/>`,
    spi: `<path d="M200 206 L200 214 M188 220 Q200 226 212 220" fill="none" stroke="${C.atrament}" stroke-width="4" stroke-linecap="round"/>`,
  }[usta];
  return `
  <!-- uszy: lewe liliowe z plamą, prawe granatowe; czubki załamane -->
  <path d="M120 128 C104 96 112 58 146 48 C166 44 178 62 176 84Z" fill="${C.lil}"/>
  <path d="M146 48 C122 46 96 66 92 100 C110 88 132 78 160 70Z" fill="${C.lilC}"/>
  <ellipse cx="114" cy="86" rx="9" ry="12" fill="${C.granat}" transform="rotate(-30 114 86)"/>
  <path d="M280 128 C296 96 288 58 254 48 C234 44 222 62 224 84Z" fill="${C.granat}"/>
  <path d="M254 48 C278 46 304 66 308 100 C290 88 268 78 240 70Z" fill="${C.granatJ}"/>

  <!-- głowa -->
  <path d="${GLOWA}" fill="${C.granat}"/>
  <g clip-path="url(#${id}-g)">
    <path d="M60 40 L198 40 C190 110 180 150 160 260 L60 260Z" fill="${C.lil}"/>
    <ellipse cx="128" cy="172" rx="20" ry="16" fill="${C.granat}" transform="rotate(20 128 172)"/>
    <ellipse cx="162" cy="96" rx="13" ry="9" fill="${C.granat}"/>
    <ellipse cx="112" cy="132" rx="7" ry="10" fill="${C.lilC}"/>
  </g>
  <!-- koralowe policzki -->
  <ellipse cx="140" cy="210" rx="26" ry="20" fill="${C.koral}"/>
  <ellipse cx="260" cy="210" rx="26" ry="20" fill="${C.koral}"/>
  <!-- biała strzałka i pysk -->
  <path d="M190 72 C196 66 210 66 214 74 C212 100 206 124 216 144 C240 160 256 182 252 210 C248 240 226 258 200 260 C174 258 152 240 148 210 C144 182 160 160 184 144 C192 124 190 98 190 72Z" fill="${C.bialy}"/>
  <!-- czupryna -->
  <path d="M174 80 L178 56 L190 70 L198 48 L208 68 L222 54 L224 80Z" fill="${C.granat}"/>
  <path d="M192 76 C190 62 198 54 206 56 C202 62 204 68 210 70 L212 80Z" fill="${C.bialy}"/>
  <!-- brwi -->
  <path d="${brewL}" fill="none" stroke="${C.lilC}" stroke-width="6" stroke-linecap="round"/>
  <path d="${brewP}" fill="none" stroke="${C.granatJ}" stroke-width="6" stroke-linecap="round"/>
  <!-- oczy: brązowe po liliowej stronie, niebieskie w granatowej -->
  ${oko(160, 152, C.braz, oczy[0])}
  ${oko(240, 152, C.niebieski, oczy[1], C.lilJ)}
  <!-- nos -->
  <path d="M178 190 C178 178 222 178 222 190 C222 202 208 208 200 208 C192 208 178 202 178 190Z" fill="${C.atrament}"/>
  <ellipse cx="192" cy="186" rx="7" ry="3.5" fill="#fff" opacity=".35"/>
  ${pysk}`;
}

// ---------- ciało na siedząco (pod głową) ----------
function lapa(cx, cy, r = 17) {
  return `<g><circle cx="${cx}" cy="${cy}" r="${r}" fill="${C.bialy}"/>
    <ellipse cx="${cx}" cy="${cy + 3}" rx="${r * .42}" ry="${r * .34}" fill="${C.atrament}"/>
    <circle cx="${cx - r * .5}" cy="${cy - r * .38}" r="${r * .16}" fill="${C.atrament}"/><circle cx="${cx - r * .17}" cy="${cy - r * .58}" r="${r * .16}" fill="${C.atrament}"/>
    <circle cx="${cx + r * .17}" cy="${cy - r * .58}" r="${r * .16}" fill="${C.atrament}"/><circle cx="${cx + r * .5}" cy="${cy - r * .38}" r="${r * .16}" fill="${C.atrament}"/></g>`;
}
function cialo(p) {
  const { lewaGora = false, prawaGora = false, lapaBroda = false } = p;
  // ramię uniesione: od barku do łapy
  const ramie = (x0, y0, x1, y1) => `<path d="M${x0} ${y0} Q${(x0 + x1) / 2} ${y0 - 10} ${x1} ${y1}" fill="none" stroke="${C.granat}" stroke-width="30" stroke-linecap="round"/>
    <path d="M${(x0 * .4 + x1 * .6)} ${(y0 * .4 + y1 * .6) - 4} L${x1} ${y1}" stroke="${C.bialy}" stroke-width="30" stroke-linecap="round"/>${lapa(x1, y1, 19)}`;
  return `
  <!-- cień -->
  <ellipse cx="200" cy="458" rx="150" ry="10" fill="${C.cien}"/>
  <!-- ogon: puszysty, z liliowym końcem -->
  <path d="M104 440 C52 444 28 404 42 366 C50 346 72 340 80 356 C72 384 84 410 116 418Z" fill="${C.bialy}" stroke="${C.krem}" stroke-width="2"/>
  <path d="M42 366 C50 346 72 340 80 356 C78 368 64 376 48 374Z" fill="${C.lil}"/>
  <!-- tułów -->
  <path d="M138 236 C96 280 78 350 82 418 Q84 452 120 452 L280 452 Q316 452 318 418 C322 350 304 280 262 236Z" fill="${C.granat}"/>
  <!-- biodra: lewe liliowe merle, prawe granatowe -->
  <circle cx="122" cy="398" r="58" fill="${C.lil}"/>
  <ellipse cx="104" cy="380" rx="13" ry="17" fill="${C.granat}"/><circle cx="140" cy="364" r="8" fill="${C.granat}"/><circle cx="124" cy="420" r="6" fill="${C.lilC}"/>
  <circle cx="278" cy="398" r="58" fill="${C.granat}"/>
  <path d="M298 352 C314 368 320 392 314 410 C302 398 294 376 292 360Z" fill="${C.lil}"/>
  <!-- tylne łapy -->
  <ellipse cx="96" cy="450" rx="36" ry="15" fill="${C.bialy}"/>
  <ellipse cx="304" cy="450" rx="36" ry="15" fill="${C.bialy}"/>
  <!-- biała puszysta pierś -->
  <path d="M148 240 C134 290 140 336 156 366 Q166 378 174 368 Q186 384 200 374 Q214 384 226 368 Q234 378 244 366 C260 336 266 290 252 240Z" fill="${C.bialy}"/>
  <!-- przednie łapy -->
  ${lewaGora ? '' : `<rect x="152" y="350" width="44" height="102" rx="22" fill="${C.bialy}"/><path d="M164 442 v9 M174 442 v10 M184 442 v9" stroke="${C.krem}" stroke-width="3" stroke-linecap="round"/>`}
  ${prawaGora || lapaBroda ? '' : `<rect x="204" y="350" width="44" height="102" rx="22" fill="${C.bialy}"/><path d="M216 442 v9 M226 442 v10 M236 442 v9" stroke="${C.krem}" stroke-width="3" stroke-linecap="round"/>`}
  <!-- bandana z węzłem -->
  <path d="M136 238 Q200 272 264 238 L262 256 Q236 290 200 330 Q164 290 138 256Z" fill="${C.koral}"/>
  <path d="M136 238 Q200 272 264 238 L263 248 Q200 284 137 248Z" fill="${C.koralC}" opacity=".55"/>
  <path d="M140 246 C122 236 100 240 92 252 C108 256 122 256 136 254Z M140 250 C126 260 112 274 110 288 C124 280 136 268 146 256Z" fill="${C.koral}"/>
  <circle cx="142" cy="250" r="8" fill="${C.koralC}"/>
  <text x="200" y="306" text-anchor="middle" font-family="Fraunces, Georgia, serif" font-weight="700" font-size="30" fill="${C.zoltyJ}">4</text>
  ${lewaGora ? ramie(162, 310, 94, 226) : ''}
  ${prawaGora ? ramie(238, 310, 306, 226) : ''}`;
}
function lapaPrzyBrodzie() {
  return `<path d="M236 330 Q262 300 232 268" fill="none" stroke="${C.granat}" stroke-width="28" stroke-linecap="round"/>${lapa(226, 262, 18)}`;
}

const POZY = {
  siedzi: { glowa: { usta: 'usmiech' }, cialo: {} },
  macha: { glowa: { usta: 'otwarte' }, cialo: { prawaGora: true }, iskry: 'p' },
  mysli: { glowa: { usta: 'plaskie', oczy: ['gora', 'gora'], brwi: 'gora' }, cialo: { lapaBroda: true }, przod: lapaPrzyBrodzie, tilt: 6 },
  radosc: { glowa: { usta: 'otwarte', oczy: ['zamkniete', 'zamkniete'] }, cialo: { lewaGora: true, prawaGora: true }, iskry: 'lp' },
  mruga: { glowa: { usta: 'usmiech', oczy: ['otwarte', 'zamkniete'] }, cialo: {}, tilt: -6 },
  smutny: { glowa: { usta: 'smutne', brwi: 'smutne' }, cialo: {}, tilt: 4 },
  spi: { glowa: { usta: 'spi', oczy: ['spi', 'spi'] }, cialo: {}, tilt: -10, zzz: true },
};

function iskry(strona) {
  const k = (x, y, s) => `<g stroke="${C.zolty}" stroke-width="7" stroke-linecap="round" transform="translate(${x} ${y}) scale(${s} 1)"><path d="M0 0 l-12 -18 M14 -6 l4 -22 M24 10 l20 -8"/></g>`;
  return (strona.includes('p') ? k(318, 210, 1) : '') + (strona.includes('l') ? k(82, 210, -1) : '');
}

function wafel(poza = 'siedzi') {
  const id = 'wafel-' + poza;
  if (poza === 'glowa') {
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="60 20 280 280" width="400" height="400" role="img" aria-label="Wafel">
  <defs><clipPath id="${id}-g"><path d="${GLOWA}"/></clipPath><clipPath id="${id}-k"><circle cx="200" cy="160" r="138"/></clipPath></defs>
  <circle cx="200" cy="160" r="138" fill="${C.zoltyJ}"/>
  <g clip-path="url(#${id}-k)">
    <path d="M110 270 Q200 250 290 270 L310 320 L90 320Z" fill="${C.granat}"/>
    <path d="M150 262 Q200 280 250 262 L240 320 L160 320Z" fill="${C.bialy}"/>
    <path d="M146 254 Q200 282 254 254 L252 268 Q230 296 200 326 Q170 296 148 268Z" fill="${C.koral}"/>
    <text x="200" y="306" text-anchor="middle" font-family="Fraunces, Georgia, serif" font-weight="700" font-size="28" fill="${C.zoltyJ}">4</text>
    <g transform="translate(200 168) scale(.8) rotate(-6) translate(-200 -160)">${glowa({ id, usta: 'usmiech', oczy: ['otwarte', 'zamkniete'] })}</g>
  </g>
</svg>`;
  }
  const P = POZY[poza];
  if (!P) throw new Error('Nieznana poza: ' + poza);
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 20 400 450" width="400" height="450" role="img" aria-label="Wafel">
  <defs><clipPath id="${id}-g"><path d="${GLOWA}"/></clipPath></defs>
  ${cialo(P.cialo)}
  <g transform="rotate(${P.tilt || 0} 200 250)">${glowa({ id, ...P.glowa })}</g>
  ${P.przod ? P.przod() : ''}
  ${P.iskry ? iskry(P.iskry) : ''}
  ${P.zzz ? `<text x="282" y="70" font-family="Caveat, cursive" font-weight="700" font-size="40" fill="${C.lilC}">z z z</text>` : ''}
</svg>`;
}

const WSZYSTKIE = [...Object.keys(POZY), 'glowa'];
module.exports = { wafel, WSZYSTKIE, KOLORY: C };

if (require.main === module) {
  const svgDir = path.join(__dirname, 'svg'); fs.mkdirSync(svgDir, { recursive: true });
  for (const p of WSZYSTKIE) fs.writeFileSync(path.join(svgDir, `wafel-${p}.svg`), wafel(p));
  if (process.argv.includes('--png')) {
    const { chromium } = require('playwright');
    (async () => {
      const pngDir = path.join(__dirname, 'png'); fs.mkdirSync(pngDir, { recursive: true });
      const b = await chromium.launch(); const pg = await b.newPage({ viewport: { width: 800, height: 980 } });
      for (const p of WSZYSTKIE) {
        const h = p === 'glowa' ? 800 : 900;
        await pg.setViewportSize({ width: 800, height: h });
        await pg.setContent(`<body style="margin:0;background:transparent">${wafel(p).replace(/width="400" height="(450|400)"/, 'width="800" height="' + h + '"')}</body>`);
        await pg.screenshot({ path: path.join(pngDir, `wafel-${p}.png`), omitBackground: true });
      }
      // arkusz podglądu
      await pg.setViewportSize({ width: 1700, height: 290 });
      await pg.setContent(`<body style="margin:0;background:#FFF6EE;display:flex;gap:10px;padding:20px;align-items:center">${WSZYSTKIE.map(p => `<div style="width:200px;height:250px;display:flex;align-items:center">${wafel(p).replace(/ width="400" height="(450|400)"/, ' width="200"')}</div>`).join('')}</body>`);
      await pg.screenshot({ path: path.join(__dirname, 'wafel-arkusz.png') });
      await b.close();
    })();
  }
}
