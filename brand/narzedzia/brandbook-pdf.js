// Brandbook HTML → PDF (A4). Uruchom: node brand/narzedzia/brandbook-pdf.js
const { chromium } = require('playwright'); const path = require('path');
(async () => {
  const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1240, height: 1754 } });
  await p.goto('file://' + path.join(__dirname, '..', 'brandbook', 'index.html'), { waitUntil: 'networkidle' });
  await p.evaluate(async () => { document.querySelectorAll('img[loading=lazy]').forEach(i => i.loading = 'eager'); await document.fonts.ready; });
  await p.waitForTimeout(800);
  await p.pdf({ path: path.join(__dirname, '..', 'brandbook', 'guliwera4-brandbook.pdf'), format: 'A4', printBackground: true, preferCSSPageSize: true,
    displayHeaderFooter: true, headerTemplate: '<span></span>',
    footerTemplate: '<div style="font-size:7pt;color:#7A6580;width:100%;padding:0 12mm;display:flex;justify-content:space-between;font-family:Arial"><span>Brandbook Guliwera 4 · v1.0</span><span class="pageNumber"></span></div>' });
  await b.close(); console.log('PDF OK');
})();
