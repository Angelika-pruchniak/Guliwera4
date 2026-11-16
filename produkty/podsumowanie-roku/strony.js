// Treść zeszytu „Pochwal się! Podsumowanie roku 2026”. Każda funkcja = jedna strona A4.
// Pola: data-pole (tekst, wielowierszowe gdy data-multi), data-check (kratka). buduj.js zamienia je na pola formularza PDF.
const fs = require('fs'), path = require('path');
const { wafel } = require('../../brand/wafel/generuj-wafla.js');

const ROK = 2026, NOWY = 2027;
const TYTUL = 'Pochwal się!';
const logo = fs.readFileSync(path.join(__dirname, 'zasoby/logo-poziome.svg'), 'utf8').replace(/ width="\d+" height="\d+"/, '');
const pies = (n, w) => `<div class="wafel" style="width:${w}">${wafel(n).replace(/ width="400" height="\d+"/, '')}</div>`;

// --- pomocnicze ---
const ln = (id, n, lh = '8mm') => `<div class="pole linie" data-pole="${id}" data-multi style="height:calc(${n} * ${lh});--lh:${lh}"></div>`;
const wr = (id, extra = '') => `<div class="pole wiersz" data-pole="${id}" ${extra}></div>`;
const rm = (id, h) => `<div class="pole ramka" data-pole="${id}" data-multi style="height:${h}"></div>`;
const ck = id => `<span class="kratka" data-check="${id}"></span>`;
const numerowane = (pref, od, ile) => Array.from({ length: ile }, (_, i) => `<div class="numer"><b>${od + i}</b>${wr(`${pref}${od + i}`)}</div>`).join('');

let licznik = 0;
const ROZDZ = { zyski: 'Część 1 · Zyski', lekcje: 'Część 2 · Lekcje', energia: 'Część 3 · Energia', '': `Podsumowanie roku ${ROK}` };
function S(klasa, tresc, { bezRamki = false } = {}) {
  licznik++;
  if (bezRamki) return `<section class="strona ${klasa}">${tresc}</section>`;
  return `<section class="strona ${klasa}">
  <div class="gora"><span class="rozdz">${ROZDZ[klasa] ?? ''}</span><span>${TYTUL} · ${ROK}</span></div>
  ${tresc}
  <div class="dol"><span>Guliwera 4 · guliwera4.pl</span><span>${licznik}</span></div>
</section>`;
}

const strony = [];

// 1. OKŁADKA
strony.push(() => S('okladka', `
  <div class="kolo" style="width:150mm;height:150mm;right:-50mm;top:-40mm"></div>
  <div class="kolo" style="width:90mm;height:90mm;left:-36mm;bottom:20mm;opacity:.7"></div>
  <div style="position:absolute;left:18mm;top:18mm;width:52mm">${logo}</div>
  <div style="position:absolute;left:18mm;right:18mm;top:62mm">
    <span class="pigulka">Zeszyt ćwiczeń · do druku i na tablet</span>
    <h1 style="font-size:58pt;color:var(--gr);margin:7mm 0 3mm">Pochwal się!</h1>
    <div style="font-family:Fraunces,serif;font-size:21pt;font-weight:600;color:var(--gr);line-height:1.15;max-width:150mm">Podsumowanie roku ${ROK}.<br>Przypomnij sobie, co Ci się udało, i&nbsp;wejdź w&nbsp;${NOWY} z&nbsp;energią.</div>
    <div class="reka" style="color:var(--koral-c);font-size:24pt;margin-top:6mm;transform:rotate(-3deg);display:inline-block">rok ${ROK}: zrobione ✓</div>
  </div>
  <div style="position:absolute;right:6mm;bottom:20mm">${pies('macha', '112mm')}</div>
  <div style="position:absolute;left:18mm;bottom:14mm;font-size:8.5pt;color:var(--gr);max-width:85mm">
    <b>Z Waflem</b>, owczarkiem australijskim Guliwera 4, który przyjmuje klientów i ocenia każdy sukces merdaniem.
  </div>`, { bezRamki: true }));

// 2. CZEŚĆ
strony.push(() => S('', `
  <h2>Cześć! Zanim zaczniesz</h2>
  <p class="lead">Ten zeszyt ma jedno zadanie: pokazać Ci czarno na białym, <span class="zakres">ile zrobiłaś albo zrobiłeś w&nbsp;${ROK}</span>. Nie robimy tu rachunku sumienia. Robimy przegląd sukcesów, wyciągamy kilka mądrych lekcji i ładujemy baterie na ${NOWY}.</p>
  <div class="siatka k3" style="margin-bottom:6mm">
    <div class="karta" style="background:var(--brzo)"><span class="etyk" style="color:var(--koral-c)">Część 1 · Zyski</span><h3>Co Ci się udało</h3><p class="mala" style="color:var(--atr)">Oś czasu, 50 sukcesów, liczby roku, ludzie, odwaga. I&nbsp;dyplom.</p></div>
    <div class="karta" style="background:var(--lil-j)"><span class="etyk" style="color:#6E58A8">Część 2 · Lekcje</span><h3>Czego nauczył Cię ten rok</h3><p class="mala" style="color:var(--atr)">Koło obszarów, bilans energii, praca i&nbsp;firma, co zostawiasz w&nbsp;${ROK}.</p></div>
    <div class="karta" style="background:var(--zol-j)"><span class="etyk" style="color:#B07A00">Część 3 · Energia</span><h3>Z czym wchodzisz w ${NOWY}</h3><p class="mala" style="color:var(--atr)">Mocne strony, słowo roku, 3 zamiary, list z przyszłości.</p></div>
  </div>
  <div class="siatka k2" style="gap:6mm">
    <div>
      <h3>Jak z tego korzystać</h3>
      <p><b>Trzy posiedzenia po 45–60 minut</b>, jedno na każdą część. Możesz wszystko zrobić w jeden wieczór, ale sukcesy lubią, kiedy poświęca się im osobną chwilę.</p>
      <p><b>Na tablecie:</b> każde pole jest do wypełnienia. Otwórz plik w aplikacji, która obsługuje formularze PDF (np. Adobe Acrobat Reader, Notatki na iPadzie, Xodo), i zapisuj co jakiś czas.</p>
      <p><b>Na papierze:</b> drukuj w kolorze lub w skali szarości, A4, „rzeczywisty rozmiar”.</p>
    </div>
    <div>
      <h3>Trzy zasady</h3>
      <p><b>1. Małe się liczy.</b> Nowy przepis, trudny mail, który w końcu poszedł. To wszystko trafia na listę.</p>
      <p><b>2. Najpierw zyski.</b> O tym, co nie wyszło, też porozmawiamy, ale dopiero w części 2. Taka kolejność jest celowa.</p>
      <p><b>3. Nie ma złych odpowiedzi.</b> Ten zeszyt czytasz tylko Ty (i&nbsp;ewentualnie pies).</p>
      <div style="display:flex;gap:4mm;align-items:flex-end;margin-top:4mm">
        ${pies('mruga', '34mm')}
        <div class="dymek" style="flex:1">Hau! Jestem Wafel. Będę tu pilnować, żeby nikt nie umniejszał swoich sukcesów.</div>
      </div>
    </div>
  </div>
  <div class="karta akc" style="margin-top:6mm;background:var(--zol-j)"><span class="etyk" style="color:#B07A00">Zanim zaczniesz: z czym chcesz wyjść z tego zeszytu?</span>${ln('intencja', 3)}</div>
  <p class="mala" style="margin-top:auto;margin-bottom:6mm">Angelika Pruchniak-Gąszczyk, Guliwera 4 · strategia, produkty cyfrowe i dobry humor</p>`));

// 3. SPIS + RYTUAŁ STARTU
strony.push(() => S('', `
  <div class="siatka k2" style="gap:8mm">
    <div>
      <h2>Co Cię czeka</h2>
      <div style="display:grid;grid-template-columns:1fr auto;row-gap:1.6mm;font-size:9.5pt;margin-top:3mm">
        <b style="color:var(--koral-c)">Część 1 · Zyski</b><span></span>
        <span>Rok w pigułce</span><span>5</span>
        <span>Oś czasu: 12 najlepszych momentów</span><span>6</span>
        <span>50 rzeczy, które Ci się udały</span><span>8</span>
        <span>Podium roku</span><span>10</span>
        <span>Liczby roku: życie i praca</span><span>11</span>
        <span>Ludzie roku i wdzięczność</span><span>13</span>
        <span>Odwaga, pierwsze razy, komplementy</span><span>14</span>
        <span>Dyplom</span><span>15</span>
        <b style="color:#6E58A8;margin-top:2mm">Część 2 · Lekcje</b><span></span>
        <span>Koło 8 obszarów ${ROK}</span><span>17</span>
        <span>Bilans energii</span><span>18</span>
        <span>Praca i firma: co działało</span><span>19</span>
        <span>10 lekcji roku</span><span>20</span>
        <span>Zostawiam i zabieram</span><span>21</span>
        <b style="color:#B07A00;margin-top:2mm">Część 3 · Energia</b><span></span>
        <span>Mocne strony z dowodami</span><span>23</span>
        <span>Słowo roku i koło ${NOWY}</span><span>24</span>
        <span>3 zamiary zamiast 30 postanowień</span><span>25</span>
        <span>List z grudnia ${NOWY}</span><span>26</span>
        <span>Pochwal się na 1 stronie</span><span>27</span>
        <span>Rytuał zamknięcia roku</span><span>28</span>
        <span>Co dalej</span><span>29</span>
      </div>
    </div>
    <div>
      <h2>Rytuał startu</h2>
      <p class="mala" style="font-size:9.5pt;color:var(--atr)">Zaznacz, co już masz. Pięć minut przygotowania oszczędza pół godziny drapania się po głowie.</p>
      <div class="lista-check" style="margin:4mm 0 6mm">
        <div>${ck('start_napoj')}<span>Ulubiona kawa, herbata albo kakao</span></div>
        <div>${ck('start_telefon')}<span>Telefon wyciszony (poza galerią zdjęć)</span></div>
        <div>${ck('start_galeria')}<span>Galeria zdjęć z ${ROK} otwarta</span></div>
        <div>${ck('start_kalendarz')}<span>Kalendarz ${ROK} pod ręką (papierowy lub w telefonie)</span></div>
        <div>${ck('start_muzyka')}<span>Playlista, przy której chce się żyć</span></div>
        <div>${ck('start_kocyk')}<span>Wygodne miejsce, najlepiej z kocem</span></div>
        <div>${ck('start_pies')}<span>Pies, kot lub inny kibic w pobliżu (opcjonalnie)</span></div>
      </div>
      <div class="karta akc" style="background:var(--zol-j)">
        <span class="etyk" style="color:#B07A00">Podpowiedź</span>
        <p style="margin:0">Przewiń galerię zdjęć od stycznia. To najszybszy sposób, żeby przypomnieć sobie rzeczy, o&nbsp;których pamięć już dawno zapomniała.</p>
      </div>
      <div style="margin-top:6mm"><span class="etyk" style="color:var(--atr6)">Data i miejsce, w którym zaczynam</span>${wr('start_data')}</div>
    </div>
  </div>`));

// 4. ROZDZIAŁ 1
const dzial = (klasa, nr, tytul, opis, nastroj) => () => S(`${klasa} dzial`, `
  <div class="kolo" style="width:170mm;height:170mm;right:-60mm;top:-50mm"></div>
  <div class="kolo" style="width:80mm;height:80mm;left:-30mm;bottom:40mm"></div>
  <div class="w">
    <div class="nr">${nr}</div>
    <span class="pigulka">${ROZDZ[klasa]}</span>
    <h1>${tytul}</h1>
    <p class="lead" style="max-width:120mm">${opis}</p>
  </div>
  <div class="pies">${pies(nastroj, '100%')}</div>`, { bezRamki: true });

strony.push(dzial('zyski', '1', 'Co Ci się udało', 'Zaczynamy od najprzyjemniejszego. Zbierzesz dowody, że ten rok był pełniejszy, niż się wydaje w&nbsp;grudniowym zmęczeniu. Nie oceniaj, tylko notuj.', 'radosc'));

// 5. ROK W PIGUŁCE
strony.push(() => S('zyski', `
  <h2>Rok ${ROK} w pigułce</h2>
  <p class="lead">Rozgrzewka. Odpowiadaj szybko, pierwszym skojarzeniem.</p>
  <div class="siatka k2" style="gap:5mm">
    <div class="karta" style="grid-column:1/-1"><span class="etyk">Gdyby ${ROK} był filmem, jego tytuł brzmiałby…</span>${wr('pig_tytul')}</div>
    <div class="karta"><span class="etyk">3 słowa, które opisują mój rok</span>${numerowane('pig_slowo', 1, 3)}</div>
    <div class="karta"><span class="etyk">Piosenka roku</span>${wr('pig_piosenka')}<span class="etyk" style="margin-top:3mm">Kolor roku</span>${wr('pig_kolor')}<span class="etyk" style="margin-top:3mm">Zapach lub smak roku</span>${wr('pig_smak')}</div>
    <div class="karta"><span class="etyk">Najlepszy dzień roku</span>${ln('pig_dzien', 3)}</div>
    <div class="karta"><span class="etyk">Najzabawniejsza sytuacja</span>${ln('pig_smiech', 3)}</div>
    <div class="karta"><span class="etyk">Coś, co w ${ROK} pokochałam / pokochałem</span>${ln('pig_milosc', 2)}</div>
    <div class="karta"><span class="etyk">Najlepszy zakup roku</span>${ln('pig_zakup', 2)}</div>
    <div class="karta akc" style="grid-column:1/-1"><span class="etyk">${ROK} w jednym zdaniu, zaczynając od „W tym roku udało mi się…”</span>${ln('pig_zdanie', 2)}</div>
  </div>`));

// 6–7. OŚ CZASU
const MIES = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];
const os = (od, intro) => () => S('zyski', `
  <h2>Oś czasu: 12 dobrych momentów <span class="reka" style="font-size:16pt">${od === 0 ? 'styczeń–czerwiec' : 'lipiec–grudzień'}</span></h2>
  ${intro}
  <div style="display:flex;flex-direction:column;gap:3.2mm;position:relative;padding-left:9mm" class="rosnij">
    <div style="position:absolute;left:3mm;top:2mm;bottom:12mm;width:.6mm;background:var(--koral);opacity:.5"></div>
    ${MIES.slice(od, od + 6).map((m, i) => `
    <div class="karta" style="position:relative;padding:3mm 4mm;flex:1">
      <span style="position:absolute;left:-8.2mm;top:3.6mm;width:4.4mm;height:4.4mm;border-radius:50%;background:var(--koral);border:1mm solid var(--krem)"></span>
      <div style="display:grid;grid-template-columns:24mm 1fr 1fr;gap:4mm;align-items:start">
        <h3 style="margin-top:1mm">${m}</h3>
        <div><span class="etyk">Najlepszy moment</span>${ln(`os_${od + i + 1}_moment`, 3, '7.5mm')}</div>
        <div><span class="etyk">Mała wygrana</span>${ln(`os_${od + i + 1}_wygrana`, 3, '7.5mm')}</div>
      </div>
    </div>`).join('')}
  </div>`);
strony.push(os(0, `<p class="lead">Miesiąc po miesiącu. Pusty miesiąc? Zajrzyj do galerii, kalendarza albo wyciągu z karty. Tam zawsze coś jest.</p>`));
strony.push(os(6, `<p class="lead">Druga połowa roku. Jeśli coś przypomni Ci się z pierwszej, dopisz to na liście sukcesów na następnej stronie.</p>`));

// 8–9. 50 SUKCESÓW
strony.push(() => S('zyski', `
  <h2>50 rzeczy, które Ci się udały</h2>
  <p class="lead">Tak, pięćdziesiąt. Pierwsze 10 przyjdzie łatwo, kolejne 20 wymaga wysiłku, a&nbsp;ostatnie 20 pokaże Ci rzeczy, których nie liczysz, a&nbsp;powinnaś albo powinieneś.</p>
  <div class="siatka k2" style="gap:6mm;align-items:start">
    <div style="display:flex;flex-direction:column;gap:.6mm;--wh:8.9mm">${numerowane('suk', 1, 24)}</div>
    <div>
      <div class="karta akc" style="margin-bottom:4mm">
        <span class="etyk">Utknęło? Podpowiedzi</span>
        <ul style="margin:0;padding-left:4.5mm;font-size:9pt;line-height:1.5">
          <li>nowa umiejętność, nawet mała</li><li>trudna rozmowa, która się odbyła</li>
          <li>projekt doprowadzony do końca</li><li>coś, czego się udało NIE zrobić</li>
          <li>pomoc komuś bliskiemu</li><li>nawyk, który przetrwał dłużej niż tydzień</li>
          <li>podwyżka, nowy klient, nowe zlecenie</li><li>granica, którą udało się postawić</li>
          <li>podróż, przeprowadzka, remont</li><li>pierwszy raz w czymkolwiek</li>
          <li>książka, kurs, certyfikat</li><li>dzień, który się przetrwało z klasą</li>
        </ul>
      </div>
      <div style="display:flex;gap:3mm;align-items:flex-end">
        <div class="dymek" style="flex:1">Wafel liczy na łapach do czterech. Ty licz dalej.</div>${pies('macha', '30mm')}
      </div>
    </div>
  </div>`));
strony.push(() => S('zyski', `
  <h2>…i ciąg dalszy <span class="reka">nie odpuszczaj!</span></h2>
  <div class="siatka k2" style="gap:6mm;margin-top:3mm">
    <div style="display:flex;flex-direction:column;gap:.6mm;--wh:12mm">${numerowane('suk', 25, 13)}</div>
    <div style="display:flex;flex-direction:column;gap:.6mm;--wh:12mm">${numerowane('suk', 38, 13)}</div>
  </div>
  <div class="karta akc" style="margin-top:6mm;display:flex;gap:5mm;align-items:center">
    <div style="font-family:Fraunces,serif;font-size:30pt;font-weight:700;color:var(--koral-c);line-height:1">50/50</div>
    <div style="flex:1"><b>Udało się dobić do pięćdziesięciu?</b> Przeczytaj listę na głos (albo przynajmniej w&nbsp;myślach). Które pozycje Cię zaskoczyły?
      ${ln('suk_zaskoczenie', 2)}</div>
  </div>`));

// 10. PODIUM
strony.push(() => S('zyski', `
  <h2>Podium roku</h2>
  <p class="lead">Wybierz z listy trzy sukcesy, które znaczą dla Ciebie najwięcej. Nie te, które najlepiej wyglądają na LinkedInie, tylko te, które czujesz.</p>
  <div style="display:grid;grid-template-columns:1fr 1.15fr 1fr;gap:4mm;align-items:end;margin:4mm 0 6mm">
    ${[[2, '#EDE5FB', '50mm'], [1, '#FFC84A', '68mm'], [3, '#FFD9E4', '36mm']].map(([n, kol, h]) => `
    <div style="text-align:center">
      <div style="font-family:Fraunces,serif;font-weight:700;font-size:14pt;margin-bottom:1.5mm">${n === 1 ? 'Złoto' : n === 2 ? 'Srebro' : 'Brąz'}</div>
      <div class="karta" style="text-align:left;margin-bottom:2mm">${ln(`pod_${n}`, 3, '7mm')}</div>
      <div style="background:${kol};height:${h};border-radius:3mm 3mm 0 0;display:flex;align-items:center;justify-content:center;font-family:Fraunces,serif;font-size:34pt;font-weight:700;color:var(--gr)">${n}</div>
    </div>`).join('')}
  </div>
  <div class="siatka k2">
    <div class="karta"><span class="etyk">Co ten złoty sukces mówi o mnie?</span>${ln('pod_mowi', 6)}</div>
    <div class="karta"><span class="etyk">Co musiało się wydarzyć, żeby się udało? Co zrobiłam / zrobiłem?</span>${ln('pod_jak', 6)}</div>
    <div class="karta" style="grid-column:1/-1"><span class="etyk">Komu najbardziej chcę się tym pochwalić? (Zrób to w tym tygodniu.)</span>${wr('pod_komu')}</div>
  </div>`));

// 11–12. LICZBY ROKU
const kafel = (id, etyk, pod = '') => `<div class="karta" style="padding:3.2mm 3.6mm"><span class="etyk" style="font-size:7pt">${etyk}</span><div class="pole" data-pole="${id}" data-duze style="height:var(--kh,12mm);border-bottom:.25mm solid var(--linia)"></div>${pod ? `<div class="mala" style="font-size:7pt;margin-top:1mm">${pod}</div>` : ''}</div>`;
strony.push(() => S('zyski', `
  <h2>Liczby roku: życie</h2>
  <p class="lead">Liczby nie kłamią i świetnie się nimi chwali. Wpisz te, które pamiętasz albo możesz sprawdzić. Na resztę daj rozsądny szacunek, nikt nie robi audytu.</p>
  <div class="siatka k4" style="gap:3mm;--kh:20mm">
    ${kafel('lz_ksiazki', 'Przeczytane książki')}${kafel('lz_filmy', 'Filmy i seriale')}${kafel('lz_km', 'Kilometry', 'pieszo, biegiem, rowerem')}${kafel('lz_miejsca', 'Nowe miejsca')}
    ${kafel('lz_ludzie', 'Nowe znajomości')}${kafel('lz_urlop', 'Dni urlopu / wolne')}${kafel('lz_spotkania', 'Spotkania z bliskimi')}${kafel('lz_nauka', 'Nowe umiejętności')}
    ${kafel('lz_smiech', 'Śmiech do łez', 'mniej więcej')}${kafel('lz_spacery', 'Spacery z psem', 'albo bez psa')}${kafel('lz_wlasne1', '…………………')}${kafel('lz_wlasne2', '…………………')}
  </div>
  <div class="siatka k2" style="margin-top:5mm">
    <div class="karta akc"><span class="etyk">Liczba, która cieszy mnie najbardziej</span>${wr('lz_naj')}<span class="etyk" style="margin-top:3mm">Dlaczego?</span>${ln('lz_naj_dlaczego', 4)}</div>
    <div class="karta"><span class="etyk">Liczba, która mnie zaskoczyła</span>${wr('lz_zask')}<span class="etyk" style="margin-top:3mm">Co z nią zrobię w ${NOWY}?</span>${ln('lz_zask_co', 4)}</div>
  </div>`));
strony.push(() => S('zyski', `
  <h2>Liczby roku: praca i firma</h2>
  <p class="lead">Tu wchodzi moja ulubiona część, bo liczby to mój żywioł. Pracujesz na etacie, prowadzisz firmę albo jedno i drugie? Wypełnij, co pasuje, resztę pomiń.</p>
  <div class="siatka k4" style="gap:3mm;--kh:18mm">
    ${kafel('lp_projekty', 'Zakończone projekty')}${kafel('lp_klienci', 'Klienci / współprace')}${kafel('lp_nowi', 'Nowi klienci', 'albo nowe osoby w zespole')}${kafel('lp_szkolenia', 'Kursy i szkolenia')}
    ${kafel('lp_wystapienia', 'Wystąpienia, posty, publikacje')}${kafel('lp_rekomendacje', 'Polecenia i rekomendacje')}${kafel('lp_awanse', 'Awanse, podwyżki, nowe role')}${kafel('lp_rekord', 'Rekordowy miesiąc', 'który i w czym')}
  </div>
  <div class="karta" style="margin-top:5mm">
    <span class="etyk">Tylko dla Twoich oczu: rok ${ROK} vs ${ROK - 1}</span>
    <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:3mm;font-size:8.5pt;font-weight:700;color:var(--atr6);margin-top:2mm">
      <span>Wskaźnik</span><span>${ROK - 1}</span><span>${ROK}</span><span>Zmiana</span>
    </div>
    ${['przychód / wynagrodzenie', 'liczba klientów / projektów', 'średnia wartość zlecenia', 'godziny pracy w tygodniu', '…………………………'].map((w, i) => `
    <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:3mm;align-items:end">
      <span style="font-size:9pt;padding-bottom:1.5mm">${w}</span>${wr(`lp_t${i}_a`)}${wr(`lp_t${i}_b`)}${wr(`lp_t${i}_c`)}
    </div>`).join('')}
    <p class="mala" style="margin:2.5mm 0 0">Zmiana w dół? To też informacja, nie wyrok. Zajmiemy się nią w części 2. Teraz szukaj tego, co poszło w górę.</p>
  </div>
  <div class="karta akc" style="margin-top:4mm"><span class="etyk">Zawodowy moment, w którym pomyślałam / pomyślałem: „no proszę, umiem to”</span>${ln('lp_moment', 5)}</div>`));

// 13. LUDZIE
strony.push(() => S('zyski', `
  <h2>Ludzie roku</h2>
  <p class="lead">Nikt nie robi wielkich rzeczy sam. Nawet Wafel potrzebuje kogoś, kto rzuci piłkę.</p>
  <div class="karta" style="--wh:10mm">
    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:4mm;font-size:8pt;font-weight:700;color:var(--koral-c);letter-spacing:.1em;text-transform:uppercase"><span>Osoba</span><span>Co dla mnie zrobiła</span></div>
    ${[1, 2, 3, 4, 5].map(i => `<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:4mm">${wr(`lud_${i}_kto`)}${wr(`lud_${i}_co`)}</div>`).join('')}
  </div>
  <div class="siatka k2" style="margin-top:4mm">
    <div class="karta"><span class="etyk">Komu w tym roku zrobiło się lżej dzięki Tobie?</span>${ln('lud_pomoc', 5)}</div>
    <div class="karta"><span class="etyk">Najlepsza nowa znajomość lub współpraca</span>${ln('lud_nowa', 5)}</div>
  </div>
  <div class="karta akc" style="margin-top:4mm">
    <span class="etyk">Wdzięczność: 5 rzeczy, za które dziękuję rokowi ${ROK}</span>
    <div style="--wh:10mm">${numerowane('wdz', 1, 5)}</div>
  </div>
  <div class="karta" style="margin-top:4mm;display:flex;gap:4mm;align-items:center">
    ${ck('lud_podziekowanie')}<span><b>Wyzwanie:</b> wyślę dziś jedną wiadomość z podziękowaniem. Do:</span><div style="flex:1">${wr('lud_do_kogo')}</div>
  </div>`));

// 14. ODWAGA I KOMPLEMENTY
strony.push(() => S('zyski', `
  <h2>Odwaga, pierwsze razy i komplementy</h2>
  <div class="siatka k2" style="gap:4mm;margin-top:3mm">
    <div class="karta"><span class="etyk">W ${ROK} pierwszy raz w życiu…</span><div style="--wh:10.5mm">${numerowane('pierw', 1, 6)}</div></div>
    <div style="display:flex;flex-direction:column;gap:4mm">
      <div class="karta akc"><span class="etyk">Najodważniejsza decyzja roku</span>${ln('odw_decyzja', 3)}</div>
      <div class="karta"><span class="etyk">Co w styczniu wydawało się niemożliwe, a&nbsp;dziś jest faktem?</span>${ln('odw_niemozliwe', 3)}</div>
    </div>
    <div class="karta" style="grid-column:1/-1">
      <span class="etyk">Szuflada komplementów: najmilsze słowa, jakie padły w Twoją stronę</span>
      <p class="mala">Od szefowej, klienta, przyjaciółki, dziecka, nieznajomej w sklepie. Przepisz je dosłownie. Wrócisz do nich w gorszy dzień.</p>
      ${ln('komplementy', 7)}
    </div>
    <div class="karta" style="grid-column:1/-1"><span class="etyk">Moment, w którym naprawdę byłam z siebie dumna / byłem z siebie dumny</span>${ln('duma', 4)}</div>
  </div>`));

// 15. DYPLOM
const lapa = `<svg viewBox="0 0 60 60" width="22mm"><g fill="var(--gr)"><ellipse cx="30" cy="40" rx="13" ry="11"/><ellipse cx="14" cy="24" rx="5.5" ry="7" transform="rotate(-20 14 24)"/><ellipse cx="25" cy="15" rx="5.5" ry="7.5"/><ellipse cx="37" cy="15" rx="5.5" ry="7.5"/><ellipse cx="47" cy="24" rx="5.5" ry="7" transform="rotate(20 47 24)"/></g></svg>`;
strony.push(() => S('zyski', `
  <div style="border:1.2mm solid var(--koral);border-radius:6mm;flex:1;margin-bottom:6mm;padding:12mm 14mm;position:relative;background:#fff;display:flex;flex-direction:column;align-items:center;text-align:center">
    <div style="position:absolute;inset:2.5mm;border:.4mm solid var(--roz);border-radius:4mm;pointer-events:none"></div>
    <span class="pigulka">Oficjalnie i nieodwołalnie</span>
    <h1 style="font-size:42pt;margin:6mm 0 2mm">Dyplom</h1>
    <div class="reka" style="font-size:22pt">za udany rok ${ROK}</div>
    <p style="margin:9mm 0 1mm;font-size:11pt">Niniejszym zaświadcza się, że</p>
    <div style="width:120mm">${wr('dyp_imie')}</div>
    <p style="margin:6mm 0 1mm;font-size:11pt">ma za sobą rok pełen sukcesów, w tym:</p>
    <div style="width:140mm;text-align:left">${ln('dyp_za', 3)}</div>
    <p style="margin:6mm 0 0;font-size:11pt;max-width:130mm">oraz wiele mniejszych zwycięstw, które się liczą, nawet jeśli nikt nie dał za nie medalu. <b>Do teraz.</b></p>
    <div style="display:flex;justify-content:space-between;align-items:flex-end;width:100%;margin-top:auto">
      <div style="width:55mm;text-align:left">${wr('dyp_data')}<div class="mala">data</div></div>
      ${pies('siedzi', '50mm')}
      <div style="width:55mm;text-align:center">${lapa}<div style="border-top:.25mm solid var(--linia)" class="mala">Wafel, Komisja ds. Merdania</div></div>
    </div>
  </div>
  <p class="mala" style="text-align:center;margin-bottom:5mm">Wydrukuj i powieś. Albo zrób zrzut ekranu i ustaw jako tapetę na styczeń.</p>`));

// 16. ROZDZIAŁ 2
strony.push(dzial('lekcje', '2', 'Czego nauczył Cię ten rok', 'Teraz, z listą sukcesów w kieszeni, możesz spokojnie spojrzeć na to, co było trudne. Nie szukamy winnych. Szukamy lekcji, które warto zabrać dalej.', 'mysli'));

// 17. KOŁO
function kolo() {
  const obsz = ['Zdrowie i energia', 'Bliscy i relacje', 'Praca, kariera, firma', 'Pieniądze', 'Rozwój i nauka', 'Radość i pasje', 'Dom i otoczenie', 'Spokój i odpoczynek'];
  const R = 100, cx = 130, cy = 130; let s = '';
  const kolory = ['#FFD8C2', '#FFD9E4', '#EDE5FB', '#FFEFC7', '#FFD8C2', '#FFD9E4', '#EDE5FB', '#FFEFC7'];
  obsz.forEach((o, i) => {
    const a0 = (i / 8) * 2 * Math.PI - Math.PI / 2, a1 = ((i + 1) / 8) * 2 * Math.PI - Math.PI / 2;
    s += `<path d="M${cx} ${cy} L${cx + R * Math.cos(a0)} ${cy + R * Math.sin(a0)} A${R} ${R} 0 0 1 ${cx + R * Math.cos(a1)} ${cy + R * Math.sin(a1)}Z" fill="${kolory[i]}" stroke="#fff" stroke-width="1.5"/>`;
    const am = (a0 + a1) / 2, lx = cx + (R + 14) * Math.cos(am), ly = cy + (R + 14) * Math.sin(am);
    const anchor = Math.abs(Math.cos(am)) < .3 ? 'middle' : Math.cos(am) > 0 ? 'start' : 'end';
    s += `<text x="${lx}" y="${ly + 3}" text-anchor="${anchor}" font-family="DM Sans" font-size="8.5" font-weight="700" fill="#3A2340">${o}</text>`;
  });
  for (let k = 1; k <= 10; k++) s += `<circle cx="${cx}" cy="${cy}" r="${R * k / 10}" fill="none" stroke="#3A2340" stroke-opacity="${k === 10 ? .35 : .12}" stroke-width="${k % 5 === 0 ? 1 : .6}" ${k % 5 ? 'stroke-dasharray="2 2"' : ''}/>`;
  s += `<text x="${cx + 3}" y="${cy - R / 2 + 3}" font-size="7" fill="#7A6580" font-family="DM Sans">5</text><text x="${cx + 3}" y="${cy - R + 9}" font-size="7" fill="#7A6580" font-family="DM Sans">10</text>`;
  return { svg: `<svg viewBox="-62 -6 384 272" style="width:100%">${s}</svg>`, obsz };
}
strony.push(() => {
  const { svg, obsz } = kolo();
  return S('lekcje', `
  <h2>Koło 8 obszarów ${ROK}</h2>
  <p class="lead">Oceń każdy obszar od 1 do 10: jak było w tym roku? Na wydruku zamaluj kawałki koła do tej wartości. Na tablecie wpisz ocenę w tabelce. Nie chodzi o same dziesiątki, tylko o to, żeby zobaczyć cały obraz.</p>
  <div style="display:grid;grid-template-columns:1.05fr 1fr;gap:5mm;align-items:start">
    <div>${svg}</div>
    <div class="karta" style="padding:3mm 4mm">
      <div style="display:grid;grid-template-columns:1fr 12mm;gap:2mm;font-size:7.5pt;font-weight:700;color:#6E58A8;letter-spacing:.1em;text-transform:uppercase"><span>Obszar</span><span>1–10</span></div>
      ${obsz.map((o, i) => `<div style="display:grid;grid-template-columns:1fr 12mm;gap:2mm;align-items:end"><span style="font-size:9pt;padding-bottom:1.5mm">${o}</span>${wr(`kolo26_${i + 1}`)}</div>`).join('')}
    </div>
  </div>
  <div class="siatka k3" style="margin-top:4mm">
    <div class="karta"><span class="etyk">Najmocniejszy obszar i dlaczego</span>${ln('kolo_mocny', 9, '7.5mm')}</div>
    <div class="karta"><span class="etyk">Obszar, który prosi o uwagę</span>${ln('kolo_uwaga', 9, '7.5mm')}</div>
    <div class="karta akc"><span class="etyk">Jedna mała zmiana, która podniesie go o 1 punkt</span>${ln('kolo_zmiana', 9, '7.5mm')}</div>
  </div>`);
});

// 18. BILANS ENERGII
strony.push(() => S('lekcje', `
  <h2>Bilans energii: aktywa i pasywa</h2>
  <p class="lead">W firmie aktywa pracują na Ciebie, a pasywa trzeba spłacać. Z energią jest tak samo. Przejrzyj kalendarz ${ROK} i&nbsp;rozpisz, co dodawało Ci sił, a&nbsp;co je wysysało.</p>
  <div class="siatka k2" style="gap:4mm">
    <div class="karta" style="background:#E7F6EC"><h3 style="color:#1F7A47">Aktywa <span class="mala">(dodawało energii)</span></h3>
      ${['Ludzie', 'Zajęcia i projekty', 'Miejsca', 'Nawyki'].map((k, i) => `<span class="etyk" style="color:#1F7A47;margin-top:2mm">${k}</span>${ln(`en_plus_${i + 1}`, 3, '7.5mm')}`).join('')}
    </div>
    <div class="karta" style="background:var(--roz-j)"><h3 style="color:#B2365F">Pasywa <span class="mala">(zabierało energię)</span></h3>
      ${['Ludzie', 'Zajęcia i projekty', 'Miejsca', 'Nawyki'].map((k, i) => `<span class="etyk" style="color:#B2365F;margin-top:2mm">${k}</span>${ln(`en_minus_${i + 1}`, 3, '7.5mm')}`).join('')}
    </div>
  </div>
  <div class="karta akc" style="margin-top:4mm">
    <span class="etyk">Mój wniosek na ${NOWY}</span>
    <div style="display:grid;grid-template-columns:22mm 1fr;gap:2mm 3mm;align-items:end">
      <b style="padding-bottom:1.5mm">Więcej:</b>${wr('en_wiecej')}<b style="padding-bottom:1.5mm">Mniej:</b>${wr('en_mniej')}<b style="padding-bottom:1.5mm">Wcale:</b>${wr('en_wcale')}
    </div>
  </div>`));

// 19. PRACA I FIRMA
strony.push(() => S('lekcje', `
  <h2>Praca i firma: co naprawdę działało</h2>
  <p class="lead">Tu zakładam swój strategiczny kapelusz. Zasada 80/20 mówi, że niewielka część działań daje większość efektów. Znajdź je w swoim roku.</p>
  <div class="siatka k2" style="gap:4mm">
    <div class="karta akc"><span class="etyk">Co dało największy efekt przy najmniejszym wysiłku?</span>${ln('fir_8020', 4)}</div>
    <div class="karta"><span class="etyk">Na co poszło dużo czasu, a&nbsp;dało niewiele?</span>${ln('fir_czas', 4)}</div>
    <div class="karta"><span class="etyk">Najlepszy projekt, klient lub zadanie roku</span>${ln('fir_najlepszy', 4)}</div>
    <div class="karta"><span class="etyk">Czego dowiedziałam / dowiedziałem się o swojej pracy?</span>${ln('fir_wiedza', 4)}</div>
  </div>
  <h3 style="margin:5mm 0 2mm">Decyzja na ${NOWY}: wzmocnić, zostawić, odpuścić</h3>
  <div class="siatka k3">
    ${[['Wzmocnić', '#E7F6EC', '#1F7A47', 'działa, daje wyniki i&nbsp;radość'], ['Zostawić', 'var(--zol-j)', '#8A6100', 'potrzebne, ale bez inwestowania więcej'], ['Odpuścić', 'var(--roz-j)', '#B2365F', 'kosztuje więcej, niż daje']].map(([t, bg, c, o], i) => `
    <div class="karta" style="background:${bg}"><h3 style="color:${c};margin:0">${t}</h3><p class="mala" style="margin-bottom:1mm">${o}</p>${ln(`fir_mat_${i + 1}`, 8, '7.5mm')}</div>`).join('')}
  </div>`));

// 20. 10 LEKCJI
strony.push(() => S('lekcje', `
  <h2>10 lekcji roku ${ROK}</h2>
  <p class="lead">Co wiesz dziś, czego nie wiedziałaś albo nie wiedziałeś rok temu? O ludziach, o pracy, o sobie, o pieniądzach, o odpoczynku.</p>
  <div class="karta" style="display:flex;flex-direction:column;gap:.8mm;--wh:11mm">${numerowane('lek', 1, 10)}</div>
  <div class="siatka k2" style="margin-top:5mm">
    <div class="karta akc"><span class="etyk">Lekcja numer 1, która zmieni najwięcej</span>${ln('lek_glowna', 6)}</div>
    <div class="karta"><span class="etyk">Co powiedział(a)bym sobie 1 stycznia ${ROK}?</span>${ln('lek_list', 6)}</div>
  </div>
  <div style="display:flex;gap:4mm;align-items:flex-end;margin-top:4mm">
    ${pies('siedzi', '28mm')}
    <div class="dymek" style="flex:1">Najlepsza lekcja Wafla z ${ROK}: kanapa jest wygodniejsza, gdy się na nią zasłuży. Twoja na pewno jest mądrzejsza.</div>
  </div>`));

// 21. ZOSTAWIAM / ZABIERAM
strony.push(() => S('lekcje', `
  <h2>Zostawiam w ${ROK}, zabieram do ${NOWY}</h2>
  <p class="lead">Niektóre rzeczy zasłużyły na emeryturę: przekonania, nawyki, zadania, które od miesięcy wiszą na liście. Inne zasłużyły na awans.</p>
  <div class="siatka k2" style="gap:4mm">
    <div class="karta" style="background:var(--roz-j)"><h3>Zostawiam w ${ROK}</h3>${ln('zost', 20, '7.5mm')}</div>
    <div class="karta" style="background:#E7F6EC"><h3>Zabieram do ${NOWY}</h3>${ln('zab', 20, '7.5mm')}</div>
  </div>
  <div class="karta akc" style="margin-top:5mm">
    <span class="etyk">Mini rytuał pożegnania</span>
    <div class="siatka k2" style="gap:5mm">
      <p style="margin:0"><b>Na papierze:</b> przepisz listę „zostawiam” na osobną kartkę, przeczytaj ją i&nbsp;podrzyj na drobne kawałki (bez ognia w mieszkaniu, prosimy).</p>
      <p style="margin:0"><b>Na tablecie:</b> skreśl każdą pozycję, a&nbsp;potem zaznacz kratkę poniżej. Kliknięcie też się liczy.</p>
    </div>
    <div style="display:flex;gap:3mm;align-items:center;margin-top:3mm">${ck('zost_ok')}<b>Zostawione. Dziękuję, ${ROK}, dalej idę bez tego.</b></div>
  </div>`));

// 22. ROZDZIAŁ 3
strony.push(dzial('energia', '3', `Z czym wchodzisz w ${NOWY}`, 'Masz dowody sukcesów i lekcje w kieszeni. Teraz zamieniamy je na energię: mocne strony, słowo roku i trzy zamiary, które naprawdę da się zrealizować.', 'mruga'));

// 23. MOCNE STRONY
strony.push(() => S('energia', `
  <h2>Mocne strony z dowodami</h2>
  <p class="lead">Wróć do listy 50 sukcesów i podium. Jakie Twoje cechy i umiejętności stoją za nimi? Do każdej dopisz dowód, czyli konkretny sukces. Z dowodem trudno się kłócić.</p>
  <div class="karta" style="--wh:11.5mm">
    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:4mm;font-size:7.5pt;font-weight:700;color:#B07A00;letter-spacing:.1em;text-transform:uppercase"><span>Mocna strona</span><span>Dowód z ${ROK}</span></div>
    ${[1, 2, 3, 4, 5, 6].map(i => `<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:4mm">${wr(`moc_${i}`)}${wr(`moc_${i}_dowod`)}</div>`).join('')}
  </div>
  <div class="siatka k2" style="margin-top:5mm">
    <div class="karta"><span class="etyk">Za co inni najczęściej mnie chwalą / do czego mnie proszą?</span>${ln('moc_inni', 4)}</div>
    <div class="karta akc"><span class="etyk">Mocna strona, którą chcę w ${NOWY} wykorzystać bardziej</span>${ln('moc_bardziej', 4)}</div>
  </div>
  <div class="karta" style="margin-top:4mm;background:var(--gr);color:#fff">
    <span class="etyk" style="color:var(--zol)">Dokończ zdanie i przeczytaj je na głos</span>
    <div style="font-family:Fraunces,serif;font-size:13pt;margin-bottom:1mm">„Jestem osobą, która…”</div>
    <div class="pole" data-pole="moc_zdanie" data-multi style="height:16mm;border-bottom:.25mm solid rgba(255,255,255,.4)"></div>
  </div>`));

// 24. SŁOWO ROKU + KOŁO 2027
strony.push(() => {
  const { obsz } = kolo();
  return S('energia', `
  <h2>Słowo roku ${NOWY}</h2>
  <p class="lead">Jedno słowo, które będzie Twoim kompasem. Nie cel, tylko kierunek. Przykłady: <em class="f">odwaga, lekkość, skupienie, zdrowie, rozkwit, porządek, radość, wzrost</em>.</p>
  <div class="karta" style="background:var(--zol);text-align:center;padding:6mm">
    <span class="etyk" style="color:var(--gr)">Moje słowo na ${NOWY}</span>
    <div class="pole" data-pole="slowo" data-duze style="height:18mm;border-bottom:.35mm solid var(--gr);width:120mm;margin:0 auto"></div>
  </div>
  <div class="siatka k2" style="margin-top:4mm">
    <div class="karta"><span class="etyk">Motto lub cytat na ${NOWY}</span>${ln('motto', 2)}</div>
    <div class="karta"><span class="etyk">${NOWY} ma być rokiem, w którym czuję się…</span>${numerowane('czuje', 1, 3)}</div>
  </div>
  <h3 style="margin:5mm 0 2mm">Koło ${NOWY}: gdzie chcesz być za rok?</h3>
  <div class="karta" style="padding:3mm 4mm;--wh:9.5mm">
    <div style="display:grid;grid-template-columns:1.2fr 14mm 14mm 2fr;gap:3mm;font-size:7.5pt;font-weight:700;color:#B07A00;letter-spacing:.1em;text-transform:uppercase"><span>Obszar</span><span>${ROK}</span><span>${NOWY}</span><span>Po czym poznam, że jest lepiej?</span></div>
    ${obsz.map((o, i) => `<div style="display:grid;grid-template-columns:1.2fr 14mm 14mm 2fr;gap:3mm;align-items:end"><span style="font-size:9pt;padding-bottom:1.5mm">${o}</span>${wr(`kolo27_${i + 1}_teraz`)}${wr(`kolo27_${i + 1}_cel`)}${wr(`kolo27_${i + 1}_po`)}</div>`).join('')}
  </div>`);
});

// 25. 3 ZAMIARY
strony.push(() => S('energia', `
  <h2>3 zamiary zamiast 30 postanowień</h2>
  <p class="lead">Postanowienia noworoczne zwykle nie przeżywają lutego. Zamiary mają lepszą kondycję, bo są krótkie, Twoje i&nbsp;mają pierwszy krok na ten tydzień.</p>
  ${[1, 2, 3].map(i => `
  <div class="karta" style="margin-bottom:3.5mm;display:grid;grid-template-columns:12mm 1fr;gap:3mm">
    <div style="font-family:Fraunces,serif;font-size:30pt;font-weight:700;color:var(--zol);line-height:1">${i}</div>
    <div>
      <span class="etyk">Zamiar</span>${wr(`zam_${i}`)}
      <div class="siatka k3" style="gap:3mm;margin-top:2mm">
        <div><span class="etyk" style="color:var(--atr6)">Dlaczego to dla mnie ważne</span>${ln(`zam_${i}_dlaczego`, 4, '7mm')}</div>
        <div><span class="etyk" style="color:var(--atr6)">Pierwszy krok w 7 dni</span>${ln(`zam_${i}_krok`, 4, '7mm')}</div>
        <div><span class="etyk" style="color:var(--atr6)">Po czym poznam, że działa</span>${ln(`zam_${i}_miara`, 4, '7mm')}</div>
      </div>
    </div>
  </div>`).join('')}
  <div class="karta akc" style="display:flex;gap:4mm;align-items:center">
    ${pies('macha', '24mm')}
    <p style="margin:0"><b>Chcesz z tych zamiarów zrobić konkretny plan?</b> Ten zeszyt ma rodzeństwo: <b>Zestaw Plan ${NOWY}</b> (Strategia na 1 stronie + Planer 90 dni) prowadzi od zamiaru do kwartalnego planu działań. Szczegóły na stronie 29.</p>
  </div>`));

// 26. LIST
strony.push(() => S('energia', `
  <h2>List z grudnia ${NOWY}</h2>
  <p class="lead">Wyobraź sobie, że jest 31 grudnia ${NOWY}. Napisz do siebie list, w którym opowiadasz, co się wydarzyło. Pisz w czasie przeszłym, jakby już się udało. Z detalami, emocjami i humorem.</p>
  <div class="karta rosnij" style="margin-bottom:6mm;display:flex;flex-direction:column">
    <div class="reka" style="margin-bottom:2mm">Cześć, to ja z grudnia ${NOWY}!</div>
    <div class="pole linie" data-pole="list" data-multi style="--lh:8mm;flex:1"></div>
    <div style="display:flex;justify-content:flex-end;gap:3mm;align-items:end;margin-top:2mm"><span class="reka" style="font-size:15pt">Ściskam,</span><div style="width:50mm">${wr('list_podpis')}</div></div>
  </div>
  <p class="mala" style="margin-bottom:6mm">Podpowiedź: na papierze włóż list do koperty i wpisz w kalendarzu przypomnienie na 1 grudnia ${NOWY}. Na tablecie zapisz plik w folderze „Otworzyć w grudniu”.</p>`));

// 27. NA 1 STRONIE
strony.push(() => S('energia', `
  <h2>Pochwal się na 1 stronie</h2>
  <p class="lead" style="margin-bottom:3mm">Cały rok na jednej kartce. Przepisz najważniejsze odpowiedzi z zeszytu, wydrukuj tę stronę i powieś tam, gdzie patrzysz codziennie.</p>
  <div class="siatka" style="grid-template-columns:repeat(6,1fr);gap:3.5mm;--wh:12mm">
    <div class="karta" style="grid-column:span 4;background:var(--brzo)"><span class="etyk" style="color:var(--koral-c)">Tytuł mojego ${ROK}</span>${wr('one_tytul')}</div>
    <div class="karta" style="grid-column:span 2;background:var(--brzo)"><span class="etyk" style="color:var(--koral-c)">3 słowa</span>${wr('one_slowa')}</div>
    <div class="karta" style="grid-column:span 3"><span class="etyk" style="color:var(--koral-c)">Podium roku</span>${numerowane('one_pod', 1, 3)}</div>
    <div class="karta" style="grid-column:span 3"><span class="etyk" style="color:var(--koral-c)">Liczby roku</span>${numerowane('one_licz', 1, 3)}</div>
    <div class="karta" style="grid-column:span 2"><span class="etyk" style="color:var(--koral-c)">Osoba roku</span>${wr('one_osoba')}</div>
    <div class="karta" style="grid-column:span 4;background:var(--lil-j)"><span class="etyk" style="color:#6E58A8">Lekcja roku</span>${wr('one_lekcja')}</div>
    <div class="karta" style="grid-column:span 3;background:var(--lil-j)"><span class="etyk" style="color:#6E58A8">Zostawiam w ${ROK}</span>${ln('one_zost', 4, '8mm')}</div>
    <div class="karta" style="grid-column:span 3;background:var(--lil-j)"><span class="etyk" style="color:#6E58A8">Moja mocna strona</span>${ln('one_moc', 4, '8mm')}</div>
    <div class="karta" style="grid-column:span 2;background:var(--zol)"><span class="etyk" style="color:var(--gr)">Słowo ${NOWY}</span><div class="pole" data-pole="one_slowo" data-duze style="height:24mm;border-bottom:.3mm solid var(--gr)"></div></div>
    <div class="karta" style="grid-column:span 4;background:var(--zol-j)"><span class="etyk" style="color:#B07A00">3 zamiary na ${NOWY}</span>${numerowane('one_zam', 1, 3)}</div>
  </div>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4mm">
    <div class="reka" style="font-size:20pt;color:var(--koral-c)">Udało się. Idę dalej. ✓</div>
    <div style="width:48mm">${logo}</div>
  </div>`));

// 28. RYTUAŁ ZAMKNIĘCIA
strony.push(() => S('energia', `
  <h2>Rytuał zamknięcia roku</h2>
  <p class="lead">Ostatnia prosta. Zaznaczaj po kolei i nie przeskakuj punktu z nagrodą.</p>
  <div class="karta lista-check" style="gap:3.6mm;padding:5mm 6mm">
    ${[
      ['zam_r1', 'Lista 50 sukcesów jest pełna (albo prawie, liczy się szczerość)'],
      ['zam_r2', 'Podziękowanie wysłane do co najmniej jednej osoby'],
      ['zam_r3', 'Dyplom wydrukowany lub zapisany jako tapeta'],
      ['zam_r4', '„Pochwal się na 1 stronie” powieszone w widocznym miejscu'],
      ['zam_r5', 'Lista „zostawiam” pożegnana'],
      ['zam_r6', 'Pierwszy krok z każdego zamiaru wpisany do kalendarza'],
      ['zam_r7', `Przypomnienie w kalendarzu na 30 czerwca ${NOWY}: półroczny przegląd tego zeszytu`],
      ['zam_r8', 'Pochwalone komuś na głos: przyjaciółce, partnerowi, mamie, zespołowi albo psu'],
      ['zam_r9', 'Nagroda za cały rok odebrana (deser, wyjście, nowa książka, drzemka)'],
    ].map(([id, t]) => `<div>${ck(id)}<span>${t}</span></div>`).join('')}
  </div>
  <div class="siatka k2" style="margin-top:5mm;align-items:end">
    <div class="karta akc">
      <span class="etyk">Moja nagroda za ${ROK}</span>${wr('nagroda')}
      <span class="etyk" style="margin-top:4mm">Zamykam rok ${ROK}. Data i podpis</span>${wr('zam_podpis')}
    </div>
    <div style="display:flex;flex-direction:column;align-items:center">
      <div class="dymek" style="margin-bottom:2mm">Pochwal się w sieci z <b>#PochwalSię${ROK}</b>. Wafel lubi czytać dobre wiadomości.</div>
      ${pies('macha', '48mm')}
    </div>
  </div>`));

// 29. CO DALEJ
strony.push(() => S('', `
  <h2>Co dalej? Od podsumowania do planu</h2>
  <p class="lead">Masz za sobą najprzyjemniejszą część pracy strategicznej, czyli wiesz, co działa. Jeśli chcesz pójść krok dalej, oto materiały Guliwera 4, które zaczynają tam, gdzie kończy się ten zeszyt.</p>
  <div class="karta" style="background:var(--gr);color:#fff;display:grid;grid-template-columns:1fr 46mm;gap:5mm;align-items:center;padding:6mm">
    <div>
      <span class="pigulka" style="background:var(--zol);color:var(--gr)">Następny krok</span>
      <h3 style="font-size:17pt;margin:3mm 0 2mm;color:#fff">Zestaw Plan ${NOWY}</h3>
      <p style="margin:0;font-size:10pt">Strategia na 1 stronie + Planer 90 dni. Zamieniasz zamiary z tego zeszytu w&nbsp;kierunek, priorytety i&nbsp;plan na pierwszy kwartał. Kolejność jest prosta: <b>najpierw pochwal się ${ROK}, potem zaplanuj ${NOWY}</b>.</p>
    </div>
    <div style="text-align:center;font-size:8.5pt">guliwera4.pl/materialy</div>
  </div>
  <div class="siatka k3" style="margin-top:4mm">
    <div class="karta" style="background:var(--lil-j)"><span class="etyk" style="color:#6E58A8">Za darmo</span><h3>Akademia Guliwera</h3><p class="mala" style="color:var(--atr)">Krótkie codzienne lekcje strategii i zarządzania w aplikacji na telefon. Seria dni, punkty, poziomy.</p><b style="font-size:8.5pt">guliwera4.pl/aplikacja</b></div>
    <div class="karta" style="background:var(--brzo)"><span class="etyk" style="color:var(--koral-c)">Kanwa + przykład</span><h3>Strategia na 1 stronie</h3><p class="mala" style="color:var(--atr)">Cała strategia firmy lub kariery na jednej kartce. Dla tych, którzy lubią konkret.</p><b style="font-size:8.5pt">guliwera4.pl/materialy</b></div>
    <div class="karta" style="background:var(--roz-j)"><span class="etyk" style="color:#B2365F">7 materiałów</span><h3>Pakiet Kierunek Zysk</h3><p class="mala" style="color:var(--atr)">Przewodnik, planer, skrypty sprzedażowe, audyt i dziennik decyzji dla małych firm.</p><b style="font-size:8.5pt">guliwera4.pl/materialy</b></div>
  </div>
  <div class="karta akc" style="margin-top:4mm;background:var(--zol-j);display:flex;gap:5mm;align-items:center">
    <div style="flex:1"><span class="etyk" style="color:#B07A00">Kurs wideo</span><h3>Strategia w 6 tygodni</h3><p style="margin:0;font-size:9.5pt">Sześć tygodni, w których krok po kroku budujesz strategię, którą da się wdrożyć w małej firmie albo w swoim zespole. Z liczbami i z dobrym humorem.</p></div>
    <b style="font-size:8.5pt">guliwera4.pl</b>
  </div>
  <div style="display:flex;gap:5mm;align-items:center;margin-top:auto;margin-bottom:6mm">
    <div style="flex:1">
      <p style="margin:0 0 1mm"><b>Dziękuję, że spędzasz ten czas z Guliwera 4.</b> Napisz mi, z czego jesteś w tym roku najbardziej zadowolona albo zadowolony. Czytam wszystkie wiadomości (Wafel część z nich też).</p>
      <p class="mala" style="margin:0">Angelika Pruchniak-Gąszczyk · kontakt@guliwera4.pl</p>
    </div>
    ${pies('mruga', '34mm')}
  </div>`));

// 30. NOTATKI + KOLOFON
strony.push(() => S('', `
  <h2>Notatki</h2>
  <div class="karta rosnij" style="display:flex;flex-direction:column;margin-bottom:4mm"><div class="pole linie" data-pole="notatki" data-multi style="--lh:8mm;flex:1"></div></div>
  <p class="mala" style="font-size:7pt;margin-bottom:6mm">© ${ROK} Guliwera 4, Angelika Pruchniak-Gąszczyk. Licencja osobista: możesz drukować i wypełniać ten zeszyt na własny użytek. Nie udostępniaj pliku dalej i nie odsprzedawaj go. Licencje dla zespołów: kontakt@guliwera4.pl. Zeszyt służy do refleksji i nie jest poradą psychologiczną, prawną ani finansową. Fonty: Fraunces, DM Sans, Caveat (SIL Open Font License). Ilustracje: Wafel, Guliwera 4.</p>`));

function html() {
  licznik = 0;
  const body = strony.map(f => f()).join('\n');
  return `<!doctype html><html lang="pl"><head><meta charset="utf-8"><title>Pochwal się! Podsumowanie roku ${ROK} · Guliwera 4</title>
<link rel="stylesheet" href="styl.css"></head><body>${body}</body></html>`;
}
module.exports = { html, ROK, NOWY };
