# -*- coding: utf-8 -*-
"""Kalkulator zysku (Pakiet startowy Kierunek Zysk). Użycie: python3 kalkulator.py WYJSCIE.xlsx"""
import sys
from openpyxl import Workbook
from openpyxl.styles import Alignment, Border, Font, PatternFill, Side
from openpyxl.worksheet.datavalidation import DataValidation

INK, MUTED, CORAL, KREM, ZOLTY, LIL, ROZ = "3A2340", "7A6580", "E8512A", "FFF6EE", "FFEFC7", "EDE5FB", "FFD9E4"
f = lambda **k: Font(name="Arial", color=k.pop("color", INK), **k)
fill = lambda c: PatternFill("solid", fgColor=c)
cienka = Side(style="thin", color="F0DDCF")
ramka = Border(bottom=cienka)

wb = Workbook()
ws = wb.active
ws.title = "Kalkulator"
ws.sheet_view.showGridLines = False
for col, w in zip("ABCDE", (46, 18, 18, 22, 22)):
    ws.column_dimensions[col].width = w
for r in range(1, 40):
    for c in "ABCDEF":
        ws[f"{c}{r}"].fill = fill(KREM)

ws["A1"] = "Kalkulator zysku"
ws["A1"].font = Font(name="Georgia", size=22, bold=True, color=INK)
ws["A2"] = "Pakiet startowy Kierunek Zysk · Guliwera 4 · guliwera4.pl"
ws["A2"].font = f(size=10, color=MUTED)
ws["A3"] = "Wpisz swoje liczby w żółte pola (przykład: usługa za 300 zł). Reszta liczy się sama."
ws["A3"].font = f(size=11, italic=True, color=MUTED)

def sekcja(r, t):
    ws[f"A{r}"] = t
    ws[f"A{r}"].font = f(size=10, bold=True, color=CORAL)

sekcja(5, "TWOJE LICZBY (MIESIĘCZNIE)")
wejscia = [
    ("Cena sprzedaży jednej sztuki / usługi (zł)", 300, "cena, którą płaci klient"),
    ("Koszt zmienny na sztukę (zł)", 100, "materiał, prowizje, podwykonawca, opłaty za płatność"),
    ("Koszty stałe w miesiącu (zł)", 6000, "czynsz, ZUS, księgowość, narzędzia, Twoja pensja"),
    ("Planowana sprzedaż (sztuk w miesiącu)", 40, ""),
    ("Zysk, który chcesz osiągnąć (zł w miesiącu)", 3000, "ponad koszty stałe"),
]
for i, (et, v, opis) in enumerate(wejscia):
    r = 6 + i
    ws[f"A{r}"], ws[f"B{r}"], ws[f"C{r}"] = et, v, opis
    ws[f"A{r}"].font = f(size=11)
    ws[f"B{r}"].font = f(size=12, bold=True)
    ws[f"B{r}"].fill = fill(ZOLTY)
    ws[f"B{r}"].number_format = '#,##0.00 "zł"' if i in (0, 1, 2, 4) else '#,##0 "szt."'
    ws[f"C{r}"].font = f(size=9, color=MUTED)
    for c in "AB":
        ws[f"{c}{r}"].border = ramka
dv = DataValidation(type="decimal", operator="greaterThanOrEqual", formula1="0", showErrorMessage=True,
                    errorTitle="Liczba", error="Wpisz liczbę 0 lub większą.")
ws.add_data_validation(dv)
dv.add("B6:B10")

sekcja(12, "WYNIKI")
wyniki = [
    ("Marża na sztuce (zł)", "=B6-B7", '#,##0.00 "zł"', "tyle zostaje z każdej sprzedaży na koszty stałe i zysk"),
    ("Marża na sztuce (%)", "=IF(B6>0,B13/B6,0)", "0.0%", ""),
    ("Próg rentowności (sztuk w miesiącu)", '=IF(B13>0,ROUNDUP(B8/B13,0),"marża ≤ 0: podnieś cenę")', '#,##0 "szt."', "poniżej tej liczby firma traci"),
    ("Próg rentowności (przychód w zł)", '=IF(ISNUMBER(B15),B15*B6,"–")', '#,##0.00 "zł"', ""),
    ("Zysk przy planowanej sprzedaży (zł)", "=B9*B13-B8", '#,##0.00 "zł";[Red]-#,##0.00 "zł"', ""),
    ("Sprzedaż potrzebna do Twojego celu (sztuk)", '=IF(B13>0,ROUNDUP((B8+B10)/B13,0),"–")', '#,##0 "szt."', ""),
    ("Margines bezpieczeństwa", '=IF(AND(B9>0,ISNUMBER(B15)),(B9-B15)/B9,"–")', "0.0%", "o ile sprzedaż może spaść, zanim firma zacznie tracić"),
]
for i, (et, wz, fmt, opis) in enumerate(wyniki):
    r = 13 + i
    ws[f"A{r}"], ws[f"B{r}"], ws[f"C{r}"] = et, wz, opis
    ws[f"A{r}"].font = f(size=11)
    ws[f"B{r}"].font = f(size=12, bold=True, color=CORAL if r in (15, 17, 18) else INK)
    ws[f"B{r}"].fill = fill("FFFFFF")
    ws[f"B{r}"].number_format = fmt
    ws[f"C{r}"].font = f(size=9, color=MUTED)
    for c in "AB":
        ws[f"{c}{r}"].border = ramka

sekcja(21, "CO JEŚLI ZMIENISZ CENĘ? (ŻEBY ZAROBIĆ TYLE, ILE PRZY PLANIE)")
nag = ("Zmiana ceny", "Nowa cena", "Marża na sztuce", "Potrzebna sprzedaż", "Zmiana vs plan")
for c, t in zip("ABCDE", nag):
    ws[f"{c}22"] = t
    ws[f"{c}22"].font = f(size=10, bold=True, color=MUTED)
    ws[f"{c}22"].fill = fill(LIL)
for i, zm in enumerate((-0.2, -0.1, -0.05, 0, 0.05, 0.1)):
    r = 23 + i
    ws[f"A{r}"] = zm
    ws[f"A{r}"].number_format = '+0%;-0%;"bez zmiany"'
    ws[f"B{r}"] = f"=$B$6*(1+A{r})"
    ws[f"B{r}"].number_format = '#,##0.00 "zł"'
    ws[f"C{r}"] = f"=B{r}-$B$7"
    ws[f"C{r}"].number_format = '#,##0.00 "zł"'
    ws[f"D{r}"] = f'=IF(C{r}>0,ROUNDUP(($B$8+MAX($B$17,0))/C{r},0),"strata na każdej sztuce")'
    ws[f"D{r}"].number_format = '#,##0 "szt."'
    ws[f"E{r}"] = f'=IF(AND(ISNUMBER(D{r}),$B$9>0),D{r}/$B$9-1,"–")'
    ws[f"E{r}"].number_format = '+0%;-0%;0%'
    for c in "ABCDE":
        ws[f"{c}{r}"].font = f(size=11, bold=(zm == 0))
        ws[f"{c}{r}"].border = ramka
        ws[f"{c}{r}"].fill = fill("FFFFFF" if zm else ROZ)
        ws[f"{c}{r}"].alignment = Alignment(horizontal="left" if c == "A" else "right")

ws["A30"] = "Wniosek z przykładu: obniżka ceny o 10% wymaga ok. 20% większej sprzedaży, żeby zarobić tyle samo. Przy niskiej marży różnica jest jeszcze większa."
ws["A30"].font = f(size=10, italic=True, color=MUTED)
ws["A32"] = "Chcesz pójść dalej? Strategia na 1 stronie (39 zł) i Przewodnik Kierunek Zysk: guliwera4.pl/materialy · Lekcje na 5 minut: guliwera4.pl/akademia"
ws["A32"].font = f(size=10, color=CORAL, bold=True)
ws["A33"] = "Kalkulator pokazuje uproszczony model (jeden produkt, miesiąc). Nie zastępuje porady księgowej ani podatkowej."
ws["A33"].font = f(size=9, color=MUTED)
for r in (3, 30, 32, 33):
    ws.merge_cells(f"A{r}:E{r}")
    ws[f"A{r}"].alignment = Alignment(wrap_text=True, vertical="top")
ws.row_dimensions[30].height = 30
ws.freeze_panes = "A5"

# Instrukcja
ins = wb.create_sheet("Jak używać")
ins.sheet_view.showGridLines = False
ins.column_dimensions["A"].width = 100
tekst = [
    ("Jak używać kalkulatora (10 minut)", Font(name="Georgia", size=18, bold=True, color=INK)),
    ("1. Wpisz cenę jednej sztuki albo godziny usługi. Jeśli sprzedajesz kilka rzeczy, zacznij od tej, która daje najwięcej przychodu.", None),
    ("2. Koszt zmienny to wszystko, co płacisz za każdą kolejną sprzedaż: materiał, prowizje, opłaty za płatność, podwykonawca.", None),
    ("3. Koszty stałe to wszystko, co płacisz co miesiąc niezależnie od sprzedaży. Wlicz swoje wynagrodzenie, inaczej zysk będzie złudny.", None),
    ("4. Sprawdź próg rentowności: tyle musisz sprzedać, żeby wyjść na zero.", None),
    ("5. Spójrz na tabelę „Co jeśli zmienisz cenę?”. Zanim dasz rabat, sprawdź, ile więcej musisz sprzedać.", None),
    ("Wzór: próg rentowności = koszty stałe ÷ (cena − koszt zmienny na sztukę).", Font(name="Arial", size=11, bold=True, color=CORAL)),
    ("Pytanie albo dziwny wynik? Odpisz na maila z Pakietem startowym: kontakt@guliwera4.pl.", None),
]
for i, (t, fo) in enumerate(tekst, start=1):
    ins[f"A{i*2}"] = t
    ins[f"A{i*2}"].font = fo or f(size=11)
    ins[f"A{i*2}"].alignment = Alignment(wrap_text=True)

wb.properties.title = "Kalkulator zysku – Guliwera 4"
wb.properties.creator = "Guliwera 4"
wb.save(sys.argv[1] if len(sys.argv) > 1 else "kalkulator-zysku.xlsx")
print("ok")
