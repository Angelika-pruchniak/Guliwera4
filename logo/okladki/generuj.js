const { chromium } = require('playwright');
const P = [
 [67,'','PEŁNY PAKIET','Kierunek Zysk: 7 materiałów','Materiał 1–7 z 7','#EDE5FB','kierunek-zysk-pakiet'],
 [68,'','PRZEWODNIK + ĆWICZENIA','Co konkretnie zmienić, żeby firma zarabiała więcej?','Materiał 1 z 7','#FFD8C2','kierunek-zysk-przewodnik'],
 [69,'v3','KANWA + PRZYKŁAD','Strategia na 1 stronie','Materiał 3 z 7','#FFD8C2','strategia-na-1-stronie'],
 [70,'','DO DRUKU LUB NA TABLET','Planer 90 dni','Materiał 4 z 7','#EDE5FB','planer-90-dni'],
 [71,'v3','16 GOTOWCÓW','Skrypty i szablony sprzedażowe','Materiał 5 z 7','#FFD9E4','skrypty-i-szablony-sprzedazowe'],
 [72,'v2','AUDYT + POMYSŁY','Audyt 30 punktów i 40 testów','Materiał 6 z 7','#FFEFC7','audyt-i-bank-40-testow'],
 [73,'','LEPSZE DECYZJE','Dziennik decyzji','Materiał 7 z 7','#FFD9E4','dziennik-decyzji'],
];
const css = `@import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,700;9..40,800&family=Inter:wght@800&display=swap');
:root{--granat:#1E2A4A}*{box-sizing:border-box}html,body{margin:0}
.kw{width:400px;height:400px;display:flex;align-items:center;justify-content:center;font-family:"DM Sans",sans-serif;position:relative;overflow:hidden}
.kw::before{content:"";position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.35);right:-90px;top:-90px}
.okl{width:230px;aspect-ratio:210/297;border-radius:10px;overflow:hidden;position:relative;background:linear-gradient(160deg,#F8B68C 0%,#FDDCC6 48%,#F2BFC5 100%);padding:9% 9% 8%;display:flex;flex-direction:column;justify-content:space-between;box-shadow:0 16px 34px rgba(58,35,64,.2);transform:rotate(-3deg)}
.okl::before{content:"";position:absolute;width:75%;aspect-ratio:1;border-radius:50%;background:rgba(255,255,255,.28);right:-22%;top:-12%}
.okl::after{content:"";position:absolute;width:45%;aspect-ratio:1;border-radius:50%;background:rgba(255,255,255,.18);left:-14%;bottom:-6%}
.m{font-size:9px;font-weight:700;letter-spacing:.14em;color:var(--granat);position:relative;z-index:1}.m::before{content:"● "}
.t{position:relative;z-index:1}.tag{display:inline-block;background:var(--granat);color:#fff;font-size:8px;font-weight:700;letter-spacing:.08em;padding:3px 8px;border-radius:999px;margin-bottom:10px}
b{display:block;font-family:"Inter","DM Sans",sans-serif;font-weight:800;color:var(--granat);font-size:22px;line-height:1.05;letter-spacing:-.02em}
.d{position:relative;z-index:1;border-top:1px solid rgba(30,42,74,.25);padding-top:6px;font-size:8px;color:var(--granat);display:flex;justify-content:space-between}
.v2{background:linear-gradient(160deg,#FDDCC6 0%,#FBE6E7 55%,#F2BFC5 100%)}.v3{background:var(--granat)}.v3 .m,.v3 b,.v3 .d{color:#fff}.v3 .tag{background:#F8B68C;color:var(--granat)}.v3 .d{border-top-color:rgba(255,255,255,.3)}.v3::before{background:rgba(248,182,140,.18)}`;
(async()=>{const b=await chromium.launch();const pg=await b.newPage({viewport:{width:400,height:400},deviceScaleFactor:3});
for(const [id,v,tag,t,d,bg,slug] of P){await pg.setContent(`<style>${css}</style><div class="kw" style="background:${bg}"><div class="okl ${v}"><span class="m">KIERUNEK ZYSK</span><div class="t"><span class="tag">${tag}</span><b>${t}</b></div><div class="d"><span>guliwera4.pl</span><span>${d}</span></div></div></div>`,{waitUntil:'networkidle'});
await pg.evaluate(()=>document.fonts.ready);await pg.screenshot({path:`${slug}.png`});console.log(id,slug)}
await b.close()})();
