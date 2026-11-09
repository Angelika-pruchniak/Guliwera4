#!/usr/bin/env python3
"""Podgląd siatki profilu Instagram (12 postów, kafelki 3:4 jak w aplikacji). Wynik: social/instagram/podglad-siatki.jpg"""
import os
from PIL import Image, ImageDraw, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IG = os.path.join(ROOT, 'instagram')
# kolejność publikacji 1..12
POSTY = [
    ('zdjecia-4x5/07-portret-boucle.jpg', ''), ('png/karuzele/k1-strategia-na-1-stronie-01.png', 'karuzela'),
    ('rolki/r3-zacznij-za-darmo-okladka.jpg', 'rolka'), ('zdjecia-4x5/01-biurko-granat.jpg', ''),
    ('png/karuzele/k2-marza-a-narzut-01.png', 'karuzela'), ('rolki/r1-trzy-pytania-okladka.jpg', 'rolka'),
    ('zdjecia-4x5/09-kanapa-bialy-garnitur.jpg', ''), ('png/karuzele/k3-trzy-liczby-w-poniedzialek-01.png', 'karuzela'),
    ('rolki/r2-etat-i-wlasna-marka-okladka.jpg', 'rolka'), ('zdjecia-4x5/03-laptop-zakiet-tweed.jpg', ''),
    ('png/karuzele/k4-nowy-produkt-6-pytan-01.png', 'karuzela'), ('zdjecia-4x5/10-notes-sukienka-blekit.jpg', ''),
]
TW, TH, GAP = 360, 480, 4
font = ImageFont.truetype(os.path.expanduser('~/.fonts/DMSans%5Bopsz,wght%5D.ttf'), 22)
img = Image.new('RGB', (TW * 3 + GAP * 2, TH * 4 + GAP * 3), 'white')
d = ImageDraw.Draw(img)
for idx, (plik, typ) in enumerate(reversed(POSTY)):
    nr = len(POSTY) - idx
    im = Image.open(os.path.join(IG, plik)).convert('RGB')
    w, h = im.size  # kadr środkowy 3:4 (tak Instagram pokazuje siatkę)
    nh = int(w * 4 / 3)
    top = (h - nh) // 2 if nh <= h else 0
    im = im.crop((0, top, w, top + min(nh, h))).resize((TW, TH), Image.LANCZOS)
    x, y = (idx % 3) * (TW + GAP), (idx // 3) * (TH + GAP)
    img.paste(im, (x, y))
    d.rounded_rectangle((x + 10, y + 10, x + 54 + (len(typ) * 13 + 14 if typ else 0), y + 44), 14, fill=(30, 42, 74))
    d.text((x + 20, y + 13), f'{nr}' + (f' · {typ}' if typ else ''), font=font, fill='white')
img.save(os.path.join(IG, 'podglad-siatki.jpg'), quality=90)
print('ok')
