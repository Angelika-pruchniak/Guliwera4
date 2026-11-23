"""Wycina jednolite kremowe tło z pozy Wafla G4 (flood fill od krawędzi + miękka krawędź). Użycie: wytnij.py we.png wy.png"""
import sys
import numpy as np
from PIL import Image, ImageFilter
from scipy import ndimage

im = Image.open(sys.argv[1]).convert('RGB')
im = im.crop((5, 5, im.width - 5, im.height - 5))
a = np.asarray(im).astype(np.float32)
h, w, _ = a.shape
# kolor tła: mediana pikseli z ramki 6 px
ramka = np.concatenate([a[:6].reshape(-1, 3), a[-6:].reshape(-1, 3), a[:, :6].reshape(-1, 3), a[:, -6:].reshape(-1, 3)])
tlo = np.median(ramka, axis=0)
d = np.sqrt(((a - tlo) ** 2).sum(axis=2))
T1, T2 = 9.0, 16.0
lab, _ = ndimage.label(d < 13)
brzeg = set(np.unique(np.concatenate([lab[0], lab[-1], lab[:, 0], lab[:, -1]]))) - {0}
rdzen = np.isin(lab, list(brzeg))
odl = ndimage.distance_transform_edt(~rdzen)
alfa = np.ones((h, w), np.float32)
alfa[rdzen] = 0
pas = (~rdzen) & (odl <= 3)
alfa[pas] = np.clip((d[pas] - T1) / (T2 - T1), 0, 1)
A = Image.fromarray((alfa * 255).astype(np.uint8)).filter(ImageFilter.GaussianBlur(0.8))
# odbarwienie krawędzi z kremu (unpremultiply względem tła)
af = np.asarray(A).astype(np.float32)[..., None] / 255
kol = np.where(af > 0.05, (a - tlo * (1 - af)) / np.maximum(af, 0.05), a)
kol = np.clip(kol, 0, 255).astype(np.uint8)
wynik = Image.fromarray(np.dstack([kol, np.asarray(A)]), 'RGBA')
bb = wynik.getbbox()
wynik.crop(bb).save(sys.argv[2])
print(sys.argv[2], wynik.crop(bb).size, 'tło', tlo.round())
