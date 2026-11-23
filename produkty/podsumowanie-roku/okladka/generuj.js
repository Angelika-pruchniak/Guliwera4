// Okładka produktu do sklepu (1200×1200), w stylu okładek „Kierunek Zysk” (brand/okladki).
// node okladka/generuj.js
const path = require('path');
const { chromium } = require('playwright');
const wafelG4 = 'data:image/png;base64,' + require('fs').readFileSync(path.join(__dirname, '../zasoby/wafel-g4/radosc.png')).toString('base64');
const F = path.join(__dirname, '../fonty');
const u = n => `url(data:font/ttf;base64,${require('fs').readFileSync(path.join(F, n)).toString('base64')})`;
const css = `
@font-face{font-family:"DM Sans";src:${u('DMSans-700-normal.ttf')};font-weight:700}
@font-face{font-family:"DM Sans";src:${u('DMSans-400-normal.ttf')};font-weight:400}
@font-face{font-family:"Fraunces";src:${u('Fraunces-600-normal.ttf')};font-weight:600}
@font-face{font-family:"Caveat";src:${u('Caveat-700-normal.ttf')};font-weight:700}
*{box-sizing:border-box}html,body{margin:0}
.kw{width:400px;height:400px;background:#FFD8C2;position:relative;overflow:hidden;font-family:"DM Sans",sans-serif;display:flex;align-items:center;justify-content:center}
.kw::before{content:"";position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.35);right:-90px;top:-90px}
.okl{width:230px;aspect-ratio:210/297;border-radius:10px;overflow:hidden;position:relative;background:linear-gradient(160deg,#F8B68C 0%,#FDDCC6 48%,#F2BFC5 100%);padding:9% 9% 8%;display:flex;flex-direction:column;box-shadow:0 16px 34px rgba(58,35,64,.2);transform:rotate(-3deg)}
.okl::before{content:"";position:absolute;width:75%;aspect-ratio:1;border-radius:50%;background:rgba(255,255,255,.28);right:-22%;top:-12%}
.m{font-size:9px;font-weight:700;letter-spacing:.14em;color:#1E2A4A;position:relative}.m::before{content:"● "}
.tag{display:inline-block;background:#1E2A4A;color:#fff;font-size:7.5px;font-weight:700;letter-spacing:.08em;padding:3px 8px;border-radius:999px;margin:22px 0 8px;position:relative}
b{display:block;font-family:"Fraunces",serif;font-weight:600;color:#1E2A4A;font-size:27px;line-height:1;position:relative}
.s{font-size:9.5px;color:#1E2A4A;margin-top:6px;line-height:1.3;position:relative}
.r{font-family:Caveat,cursive;font-weight:700;color:#E8512A;font-size:14px;transform:rotate(-4deg);display:inline-block;margin-top:6px}
.pies{position:absolute;right:-4px;bottom:30px;width:112px}
.d{z-index:2;background:linear-gradient(transparent,rgba(242,191,197,.0));position:absolute;left:9%;right:9%;bottom:12px;border-top:1px solid rgba(30,42,74,.25);padding-top:5px;font-size:7.5px;color:#1E2A4A;display:flex;justify-content:space-between}`;
(async () => {
  const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 400, height: 400 }, deviceScaleFactor: 3 });
  await p.setContent(`<style>${css}</style><div class="kw"><div class="okl">
    <span class="m">PODSUMOWANIE ROKU</span><div><span class="tag">ZESZYT · 30 STRON · NA TABLET</span></div>
    <b>Pochwal się!</b><div class="s">Przypomnij sobie, co Ci się udało w&nbsp;2026</div><div class="r">rok 2026: zrobione ✓</div>
    <div class="pies"><img src="${wafelG4}" alt="" style="width:100%;display:block"></div>
    <div class="d"><span>guliwera4.pl</span><span>z Waflem</span></div></div></div>`, { waitUntil: 'load' });
  await p.evaluate(() => document.fonts.ready);
  await p.screenshot({ path: path.join(__dirname, 'pochwal-sie-podsumowanie-roku.png') });
  await b.close();
})();
