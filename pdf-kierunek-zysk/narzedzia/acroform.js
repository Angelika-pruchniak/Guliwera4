// Dodaje pola AcroForm (tekst z osadzonym DM Sans → polskie znaki, checkboxy) i metadane.
// Użycie: node acroform.js ../PDF/plik.pdf "Tytuł"
const { PDFDocument, rgb } = require('pdf-lib');
const fontkit = require('@pdf-lib/fontkit');
const fs = require('fs'); const path = require('path');
const [pdfPath, title] = process.argv.slice(2);
(async () => {
  const mapPath = pdfPath.replace(/\.pdf$/, '.pola.json');
  const fields = JSON.parse(fs.readFileSync(mapPath, 'utf8'));
  const doc = await PDFDocument.load(fs.readFileSync(pdfPath), { updateMetadata: false });
  doc.registerFontkit(fontkit);
  const font = await doc.embedFont(fs.readFileSync(path.resolve(__dirname, '../src/fonts-pola/DMSans_400Regular.ttf')), { subset: false });
  const form = doc.getForm(); const pages = doc.getPages(); const PX = 0.75;
  const atrament = rgb(0x3A / 255, 0x23 / 255, 0x40 / 255);
  for (const f of fields) {
    const pg = pages[f.page]; const H = pg.getHeight();
    const box = { x: f.x * PX, y: H - (f.y + f.h) * PX, width: f.w * PX, height: f.h * PX, borderWidth: 0, borderColor: undefined, backgroundColor: undefined };
    if (f.type === 'check') {
      const cb = form.createCheckBox(f.name); cb.addToPage(pg, { ...box, textColor: atrament });
    } else {
      const tf = form.createTextField(f.name);
      if (f.multiline) tf.enableMultiline();
      tf.addToPage(pg, { ...box, font, textColor: f.dark ? rgb(1, 1, 1) : atrament });
      tf.setFontSize(f.multiline ? 8.5 : 9);
    }
  }
  form.updateFieldAppearances(font);
  doc.setTitle(title, { showInWindowTitleBar: true });
  doc.setAuthor('Angelika Pruchniak-Gąszczyk · Guliwera 4');
  doc.setSubject('Kierunek Zysk · guliwera4.pl');
  doc.setKeywords(['strategia', 'Business Model Canvas', 'mała firma', 'Kierunek Zysk']);
  doc.setCreator('Guliwera 4'); doc.setProducer('Guliwera 4 (Chromium + pdf-lib)'); doc.setLanguage('pl-PL');
  fs.writeFileSync(pdfPath, await doc.save());
  fs.unlinkSync(mapPath);
  console.log('Pola dodane:', fields.length);
})();
