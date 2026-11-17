"""Przebudowuje sekcje stylizacji i kapsuły we wpis.html na podstawie katalog.py (realne produkty)."""
import html, pathlib, sys
sys.path.insert(0, str(pathlib.Path(__file__).parent))
from katalog import K, STYL

P = pathlib.Path(__file__).resolve().parents[1] / "wpis.html"
s = P.read_text(encoding="utf-8")
IMG = "https://guliwera4.pl/wp-content/uploads/2026/11/lookbook-jz2026-v3-stylizacja-{:02d}.png"


def li(k):
    rys, kol, nazwa, marka, sklad, cena, link, bud = K[k]
    znak = " <span class='bud'>w budżecie</span>" if bud else ""
    return (f'<li><a href="{link}" target="_blank" rel="noopener">{html.escape(nazwa)}</a> – {html.escape(marka)}, '
            f'{html.escape(sklad)} · <strong>{html.escape(cena)}</strong>{znak}</li>')


bloki = []
for i, (tytul, tag, items, klucz) in enumerate(STYL):
    n = i + 1
    bloki.append(f'''<div class="look"><h3>{n}. {html.escape(tytul)}</h3>
<figure class="plansza"><img src="{IMG.format(n)}" alt="Stylizacja {n}: {html.escape(tytul)} – produkty z marką, składem i ceną" loading="lazy"></figure>
<p><em>Klucz:</em> {html.escape(klucz)}</p>
<p class="kup">Produkty (kliknij, żeby przejść do sklepu):</p><ul class="kupl">{"".join(li(k) for k in items)}</ul></div>''')
    if n == 7:
        bloki.append('<figure><img src="https://guliwera4.pl/wp-content/uploads/2026/11/angelika-pruchniak-gaszczyk-jasny-garnitur.jpg" alt="Angelika Pruchniak-Gąszczyk w jasnym garniturze"><figcaption>Jasne kolory na rozmowy, w których chodzi o liczby.</figcaption></figure>')

a = s.index('<div class="look">'); b = s.index('<p class="g4-odrecznie">')
s = s[:a] + "\n\n".join(bloki) + "\n\n" + s[b:]

bud = [k for k, v in K.items() if v[7]]
dok = [k for k, v in K.items() if not v[7]]
suma = sum(float(K[k][5].replace("ok. ", "").replace(" zł", "").replace(" ", "").replace(",", ".")) for k in bud)
rez = 3000 - suma
fmt = lambda x: f"{x:,.2f}".replace(",", " ").replace(".", ",") + " zł"
wiersz = lambda k: f'<tr><td>{html.escape(K[k][2])}</td><td><a href="{K[k][6]}" target="_blank" rel="noopener">{html.escape(K[k][3])}</a><br><small>{html.escape(K[k][4])}</small></td><td>{html.escape(K[k][5])}</td></tr>'
kapsula = f'''<h2>Kapsuła za 3 000 zł</h2>
<p>{len(bud)} konkretnych produktów, które razem dają wszystkie 10 stylizacji. Kosztują {fmt(suma)}, a {fmt(rez)} zostaje na krawcową i szewca: skrócenie rękawów, podwinięcie spodni, fleki. Elegancję robi dopasowanie, nie metka.</p>
<div class="tw"><table>
<tr><th>Element</th><th>Produkt i skład</th><th>Cena*</th></tr>
{"".join(wiersz(k) for k in bud)}
<tr><td colspan="2">Rezerwa: krawcowa i szewc</td><td>{fmt(rez)}</td></tr>
<tr><td colspan="2"><strong>Razem</strong></td><td><strong>3 000 zł</strong></td></tr>
</table></div>
<h3>Dokup z czasem (albo weź z szafy)</h3>
<div class="tw"><table>
<tr><th>Element</th><th>Produkt i skład</th><th>Cena*</th></tr>
{"".join(wiersz(k) for k in dok)}
</table></div>
<p>Masz już podobną rzecz? Użyj swojej. Liczy się kolor i krój, nie konkretna metka.</p>

'''
a = s.index('<h2>Kapsuła'); b = s.index('<h2>Jak kupić taniej</h2>')
s = s[:a] + kapsula + s[b:]
s = s.replace("13 rzeczy kupujesz, 9 pewnie już masz. Budżet: 3 000 zł z rezerwą na krawcową.",
              f"{len(bud)} produktów w budżecie 3 000 zł (z rezerwą na krawcową), resztę dokupisz z czasem.")
s = s.replace("Pod planszą znajdziesz klikalne linki do sklepów. Pozycje „z szafy” to rzeczy, które większość z nas już ma, a „polowanie” oznacza, że szukasz w TK Maxx lub Half Price z limitem ceny.",
              "Pod planszą są klikalne linki do konkretnych produktów. Znaczek „w budżecie” oznacza rzeczy z kapsuły za 3 000 zł.")
if ".g4w .bud" not in s:
    s = s.replace(".g4w .kupl{", ".g4w .bud{display:inline-block;background:#FFEFC7;border-radius:99px;padding:0 8px;font-size:12px;font-weight:600}\n.g4w .kupl{")
P.write_text(s, encoding="utf-8")
print(len(bud), fmt(suma), fmt(rez))
