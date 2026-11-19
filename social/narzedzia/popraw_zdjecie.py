#!/usr/bin/env python3
"""Korekta zdjęcia BEZ zmiany rysów twarzy: tylko światło, cienie, balans bieli, szum i ostrość (+ powiększenie 2× klasyczną interpolacją).
Użycie: python3 social/narzedzia/popraw_zdjecie.py wejscie.jpg wyjscie.jpg [skala]"""
import sys
import cv2
import numpy as np

wej, wyj = sys.argv[1], sys.argv[2]
skala = float(sys.argv[3]) if len(sys.argv) > 3 else 2.0
img = cv2.imread(wej)

# 1. odszumienie (mocny szum z telefonu / kompresji), łagodne, żeby nie „plastikować” skóry
img = cv2.fastNlMeansDenoisingColored(img, None, 4, 4, 7, 21)

# 2. powiększenie klasyczne (bez AI, bez dorysowywania szczegółów)
img = cv2.resize(img, None, fx=skala, fy=skala, interpolation=cv2.INTER_LANCZOS4)

# 3. balans bieli: bardzo delikatnie (gray world 20%), żeby zostało ciepło światła
f = img.astype(np.float32)
srednie = f.reshape(-1, 3).mean(axis=0)
cel = srednie.mean()
f *= 1.0  # bez zmiany barw: ciepłe światło sali zostaje (zmiana bieli bladziła skórę)

# 4. światło i cienie na kanale jasności (LAB): rozjaśnienie cieni + lokalny kontrast (CLAHE)
lab = cv2.cvtColor(np.clip(f, 0, 255).astype(np.uint8), cv2.COLOR_BGR2LAB)
L, a, b = cv2.split(lab)
Lf = L.astype(np.float32) / 255
maska = np.clip((0.55 - Lf) / 0.55, 0, 1) ** 1.5   # działa tylko w cieniach
Lf = Lf + 0.16 * maska * (1 - maska * 0.3)
L = (Lf * 255).astype(np.uint8)
# (bez CLAHE: rozjaśniał twarz)
img = cv2.cvtColor(cv2.merge([L, a, b]), cv2.COLOR_LAB2BGR)

# 5. lekkie nasycenie (+6%)
hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV).astype(np.float32)
hsv[..., 1] = np.clip(hsv[..., 1] * 1.04, 0, 255)
img = cv2.cvtColor(hsv.astype(np.uint8), cv2.COLOR_HSV2BGR)

# 6. wyostrzenie (unsharp mask)
rozmyte = cv2.GaussianBlur(img, (0, 0), 1.6)
img = cv2.addWeighted(img, 1.4, rozmyte, -0.4, 0)

cv2.imwrite(wyj, img, [cv2.IMWRITE_JPEG_QUALITY, 95])
print(wyj, img.shape[1], 'x', img.shape[0])
