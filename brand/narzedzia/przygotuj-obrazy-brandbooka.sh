#!/usr/bin/env bash
# Kopiuje i zmniejsza obrazy do brand/brandbook/img (wymaga ImageMagick). Uruchom z katalogu brand/.
set -e; I=brandbook/img; rm -rf $I; mkdir -p $I/szablony $I/siatka
cp logo/svg/*.svg lilek/*.svg $I/
convert zdjecia/angelika-portret-bialy.jpg -resize 760x -quality 82 $I/portret.jpg
for f in kierunek-zysk-przewodnik strategia-na-1-stronie audyt-i-bank-40-testow planer-90-dni; do convert okladki/$f.png -resize 520x -quality 84 $I/okl-$f.jpg; done
for f in szablony/png/*.png; do n=$(basename $f .png); convert $f -resize '720x720>' -quality 84 $I/szablony/$n.jpg; done
for n in ig-1080x1350-porada ig-1080x1080-cytat story-1080x1920-porada linkedin-1200x627-link linkedin-baner-1584x396 facebook-okladka-1640x624 youtube-miniatura-1280x720 reklama-google-1200x628 linkedin-1200x1200-porada; do convert szablony/png/siatka/$n.png -resize '720x720>' -quality 84 $I/siatka/$n.jpg; done
mkdir -p $I/sesja
for f in zdjecia-sesja/*.jpg; do convert $f -resize "520x520>" -quality 80 $I/sesja/$(basename $f); done
du -sh $I
