// SVG → PNG (logo, favicony, awatary, Lilek). Uruchom: node brand/narzedzia/eksport-logo.js
const { chromium } = require('playwright'); const fs = require('fs'); const path = require('path');
const B = path.join(__dirname, '..'); const SVG = path.join(B, 'logo', 'svg');
const zadania = [
  // [plik svg, wyjście, szerokość px]
  ['znak-g4.svg', 'logo/png/znak-g4-1000.png', 1000], ['znak-g4.svg', 'logo/png/znak-g4-500.png', 500],
  ['znak-g4-atrament.svg', 'logo/png/znak-g4-atrament-1000.png', 1000], ['znak-g4-granat.svg', 'logo/png/znak-g4-granat-1000.png', 1000],
  ['znak-g4-bialy.svg', 'logo/png/znak-g4-bialy-1000.png', 1000], ['znak-g4-koral.svg', 'logo/png/znak-g4-koral-1000.png', 1000],
  ['logo-poziome.svg', 'logo/png/logo-poziome-2000.png', 2000], ['logo-poziome.svg', 'logo/png/logo-poziome-800.png', 800],
  ['logo-poziome-ciemne-tlo.svg', 'logo/png/logo-poziome-ciemne-tlo-2000.png', 2000],
  ['logo-poziome-krotkie.svg', 'logo/png/logo-poziome-krotkie-1200.png', 1200], ['logo-poziome-krotkie-ciemne-tlo.svg', 'logo/png/logo-poziome-krotkie-ciemne-tlo-1200.png', 1200],
  ['logo-osobiste.svg', 'logo/png/logo-osobiste-2000.png', 2000], ['logo-osobiste-ciemne-tlo.svg', 'logo/png/logo-osobiste-ciemne-tlo-2000.png', 2000],
  ['awatar.svg', 'logo/png/awatar-400.png', 400], ['awatar.svg', 'logo/png/awatar-1080.png', 1080], ['awatar-granat.svg', 'logo/png/awatar-granat-1080.png', 1080],
  ...[16, 32, 48, 180, 192, 512].map(s => ['ikona.svg', `logo/favicon/favicon-${s}.png`, s]),
];
for (const f of fs.readdirSync(path.join(B, 'lilek')).filter(f => f.endsWith('.svg'))) zadania.push(['../../lilek/' + f, 'lilek/' + f.replace('.svg', '-800.png'), 800]);
(async () => {
  const b = await chromium.launch(); const p = await b.newPage();
  for (const [src, out, w] of zadania) {
    const svg = fs.readFileSync(path.join(SVG, src), 'utf8'); const m = svg.match(/viewBox="0 0 ([\d.]+) ([\d.]+)"/);
    const h = Math.round(w * m[2] / m[1]);
    await p.setViewportSize({ width: w, height: h });
    await p.setContent(`<style>html,body{margin:0;background:transparent}</style><img src="data:image/svg+xml;base64,${Buffer.from(svg).toString('base64')}" style="width:${w}px;height:${h}px;display:block">`);
    fs.mkdirSync(path.dirname(path.join(B, out)), { recursive: true });
    await p.screenshot({ path: path.join(B, out), omitBackground: true }); console.log('✓', out);
  }
  await b.close();
})();
