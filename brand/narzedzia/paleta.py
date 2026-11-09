"""Źródło prawdy palety v3 „liliputy”. Uruchom: python3 brand/narzedzia/paleta.py
Tworzy brand/kolory/: kolory.css, kolory.json, kolory.gpl, guliwera4-paleta.ase, kontrasty.md"""
import json, struct, itertools, pathlib
OUT = pathlib.Path(__file__).resolve().parent.parent / "kolory"
P = [  # slug, nazwa, hex, rola
 ("krem","Krem","#FFF6EE","tło główne"),
 ("koral","Koral","#FF6B3D","akcent główny, przyciski, liczba 4"),
 ("koral-ciemny","Koral ciemny","#E8512A","linki, hover, tekst akcentowy"),
 ("roz","Róż","#F4799F","gradient znaku, podkreślenia menu"),
 ("roz-jasny","Róż jasny","#FFD9E4","koła dekoracyjne, karty"),
 ("liliowy","Liliowy","#B9A5E3","sekcje CTA, krawat Lilka"),
 ("liliowy-jasny","Liliowy jasny","#EDE5FB","karty, ramki spisu treści"),
 ("zolty","Żółty","#FFC84A","podkreślenie „zakres”, uśmiech znaku, plakietki"),
 ("zolty-jasny","Żółty jasny","#FFEFC7","ramki z ważną informacją, karty"),
 ("brzoskwinia","Brzoskwinia","#FFD8C2","tła okładek, plakietki „wkrótce”"),
 ("atrament","Atrament","#3A2340","tekst główny, nagłówki"),
 ("atrament-60","Atrament 60%","#7A6580","tekst pomocniczy, podpisy"),
 ("granat","Granat","#1E2A4A","ciemne sekcje, okładki v3, kapelusz Lilka"),
]
EXTRA = [("bialy","Biały","#FFFFFF","karty i sekcje na kremie")]
def rgb(h): h=h.lstrip('#'); return tuple(int(h[i:i+2],16) for i in (0,2,4))
def cmyk(h):
    r,g,b=[x/255 for x in rgb(h)]; k=1-max(r,g,b)
    if k>=1: return (0,0,0,100)
    return tuple(round(v*100) for v in ((1-r-k)/(1-k),(1-g-k)/(1-k),(1-b-k)/(1-k),k))
def lum(h):
    def c(v):
        v/=255; return v/12.92 if v<=0.03928 else ((v+0.055)/1.055)**2.4
    r,g,b=rgb(h); return 0.2126*c(r)+0.7152*c(g)+0.0722*c(b)
def ratio(a,b):
    la,lb=sorted((lum(a),lum(b)),reverse=True); return (la+0.05)/(lb+0.05)
def ocena(r):
    return "AAA" if r>=7 else "AA" if r>=4.5 else "AA duży" if r>=3 else "nie"
if __name__=="__main__":
    OUT.mkdir(exist_ok=True)
    allc=P+EXTRA
    css=[":root{"]+[f"  --{s}: {h}; /* {n}: {r} */" for s,n,h,r in allc]+["}"]
    (OUT/"kolory.css").write_text("/* Guliwera 4 · paleta v3 „liliputy” */\n"+"\n".join(css)+"\n")
    (OUT/"kolory.json").write_text(json.dumps([{"slug":s,"nazwa":n,"hex":h,"rgb":rgb(h),"cmyk_przyblizone":cmyk(h),"rola":r} for s,n,h,r in allc],ensure_ascii=False,indent=2))
    gpl=["GIMP Palette","Name: Guliwera 4 v3","Columns: 7","#"]+[f"{rgb(h)[0]:3d} {rgb(h)[1]:3d} {rgb(h)[2]:3d}\t{n}" for s,n,h,r in allc]
    (OUT/"kolory.gpl").write_text("\n".join(gpl)+"\n")
    # Adobe Swatch Exchange (RGB, kolory globalne)
    blocks=b""
    for s,n,h,r in allc:
        name=(n+"\0").encode("utf-16-be"); body=struct.pack(">H",len(n)+1)+name+b"RGB "+struct.pack(">fff",*[x/255 for x in rgb(h)])+struct.pack(">H",0)
        blocks+=struct.pack(">HI",1,len(body))+body
    (OUT/"guliwera4-paleta.ase").write_bytes(b"ASEF"+struct.pack(">HHI",1,0,len(allc))+blocks)
    rows=["# Kontrasty WCAG 2.2 (wyliczone automatycznie)","","AA = tekst zwykły (≥ 4,5:1). „AA duży” = tylko tekst ≥ 24 px albo ≥ 19 px pogrubiony (≥ 3:1). „nie” = nie używać do tekstu.","","| Tekst \\ Tło | "+" | ".join(n for _,n,_,_ in allc)+" |","|---|"+"---|"*len(allc)]
    for s,n,h,_ in allc:
        rows.append(f"| **{n}** | "+" | ".join(f"{ratio(h,h2):.1f} {ocena(ratio(h,h2))}" if h!=h2 else "–" for _,_,h2,_ in allc)+" |")
    (OUT/"kontrasty.md").write_text("\n".join(rows)+"\n")
    for s,n,h,_ in allc: print(f"{n:15} {h} {rgb(h)} {cmyk(h)}")
    print()
    for a,b in [("#FFFFFF","#FF6B3D"),("#FFFFFF","#E8512A"),("#3A2340","#FF6B3D"),("#1E2A4A","#FF6B3D"),("#3A2340","#FFF6EE"),("#7A6580","#FFF6EE"),("#7A6580","#FFFFFF"),("#E8512A","#FFF6EE"),("#FF6B3D","#FFF6EE"),("#FFFFFF","#B9A5E3"),("#3A2340","#B9A5E3"),("#FFC84A","#1E2A4A"),("#FF6B3D","#1E2A4A"),("#FFF6EE","#1E2A4A"),("#F4799F","#FFF6EE"),("#3A2340","#FFC84A"),("#1E2A4A","#FFD8C2"),("#3A2340","#EDE5FB"),("#7A6580","#EDE5FB"),("#7A6580","#FFEFC7"),("#E8512A","#FFFFFF"),("#FFFFFF","#3A2340"),("#1E2A4A","#F4799F"),("#FFFFFF","#F4799F"),("#3A2340","#F4799F"),("#FFD8C2","#1E2A4A"),("#B9A5E3","#1E2A4A"),("#E8512A","#FFEFC7"),("#E8512A","#FFD9E4"),("#C53D17","#FFFFFF")]:
        print(a,b,round(ratio(a,b),2),ocena(ratio(a,b)))
