#!/bin/sh
# Pobiera fonty marki (Google Fonts, licencja SIL OFL 1.1) do fonty/ jako TTF.
set -e
cd "$(dirname "$0")"; mkdir -p fonty
css=$(curl -s -A curl "https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,700&family=Caveat:wght@500;700&family=Fraunces:ital,opsz,wght@0,9..144,600;0,9..144,700;1,9..144,500")
echo "$css" | awk '/font-family/{gsub(/[^A-Za-z ]/,"",$0);sub(/fontfamily /,"");f=$0} /font-style/{s=$2} /font-weight/{w=$2} /src:/{match($0,/https:[^)]+/);u=substr($0,RSTART,RLENGTH);gsub(/[ ;]/,"",f);gsub(/;/,"",s);gsub(/;/,"",w);print f"-"w"-"s" "u}' | while read n u; do curl -sSf -o "fonty/$n.ttf" "$u"; echo "fonty/$n.ttf"; done
