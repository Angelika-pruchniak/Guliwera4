"""Katalog realnych produktów (ceny z wyszukiwarki, 17.11.2026 – do potwierdzenia na stronie sklepu przed publikacją).
(rysunek, kolor, nazwa, marka i model, skład, cena, link, w_budzecie)
"""
K = {
 "plaszcz": ("coat", "#B98A5A", "Płaszcz camel", "Molton, kamelowy płaszcz z kołnierzykiem (outlet)", "wełniany – % do sprawdzenia", "499 zł", "https://molton.pl/pl/products/kamelowy-plaszcz-z-kolnierzykiem-9443.html", True),
 "marynarka": ("blazer", "#1E2A4A", "Granatowa marynarka", "Mohito, dwurzędowa 706CZ-95X", "skład do sprawdzenia", "99,99 zł", "https://www.mohito.com/pl/pl/dwurzedowa-granatowa-marynarka-706cz-95x", True),
 "sukienka": ("dress", "#1E2A4A", "Sukienka midi kopertowa", "Ansin, granatowa sukienka midi", "dzianina", "399 zł", "https://ansin.pl/sukienka-midi-z-kopertowym-dekoltem-granatowa", True),
 "sw_ecru": ("sweater", "#EFE4D2", "Sweter ecru", "Wólczanka, sweter z wełny merino", "wełna merino", "99,99 zł", "https://wolczanka.pl/bia%C5%82y-sweter-w%C3%B3lczanka-49674-1", True),
 "sw_blekit": ("sweater", "#A9C4E2", "Błękitny sweter", "Wólczanka, gładki z merino i kaszmirem", "35% wiskoza, 30% poliamid, 30% merino, 5% kaszmir", "249,99 zł", "https://wolczanka.pl/niebieski-sweter-damski-w%C3%B3lczanka-37587-1", False),
 "spodnie_bez": ("wide", "#D9C3A5", "Beżowe spodnie", "Mohito, eleganckie 7609X-08X", "skład do sprawdzenia", "ok. 129,99 zł", "https://www.mohito.com/pl/pl/eleganckie-bezowe-spodnie-7609x-08x", True),
 "spodnica": ("skirt", "#1E2A4A", "Spódnica plisowana midi", "Reserved PREMIUM 7026X-59X", "gładka tkanina", "89,99 zł", "https://www.reserved.com/pl/pl/plisowana-spodnica-midi-7026x-59x", True),
 "czolenka": ("pumps", "#1C1A1F", "Czółenka STONE", "Kazar, czarne, obcas 5–8 cm", "skóra naturalna", "549 zł", "https://kazar.com/czarne-skorzane-czolenka-na-niewysokim-obcasie-60772-01-n0.html", True),
 "mokasyny": ("loafers", "#6B4630", "Mokasyny", "Gino Rossi CEO-LUIGI-03-125AM (CCC)", "skóra", "299,99 zł", "https://ccc.eu/pl/pl/p/mokasyny-gino-rossi-ceo-luigi-03-125am-brazowy-5906751713990", True),
 "kuferek": ("bag", "#8A5A3A", "Torebka brązowa", "Eva Minge R-01 (CCC)", "tworzywo syntetyczne", "379,99 zł", "https://ccc.eu/pl/torby/torby-damskie/torebka-eva-minge-r-01-brazowy", True),
 "perly": ("pearls", "#F4EFE6", "Naszyjnik z perłą", "W.Kruk, srebro 925, perła słodkowodna", "srebro, perła hodowlana", "179 zł", "https://wkruk.pl/bizuteria/naszyjniki/naszyjnik-srebrny-z-perla", True),
 "spodnie_gran": ("straight", "#1E2A4A", "Granatowe spodnie", "Reserved, dzianinowe w prążek 662HO-59X", "dzianina z domieszką wełny", "139,99 zł", "https://www.reserved.com/pl/pl/dzianinowe-spodnie-w-prazek-662ho-59x", True),
 # dokupisz później / jeśli nie masz
 "koszula": ("shirt", "#FFFFFF", "Biała koszula", "Wólczanka DO49WL0152", "bawełna", "249,99 zł", "https://wolczanka.pl/biala-koszula-damska-wolczanka-99", False),
 "jedwab": ("blouse", "#EFE4D2", "Kremowa koszula z jedwabiem", "Wólczanka DJ09LB8427", "92% jedwab, 8% spandex", "cena na stronie", "https://wolczanka.pl/biala-koszula-damska-lambert-118", False),
 "golf": ("golf", "#1E2A4A", "Granatowy golf", "Wólczanka, golf z merino i kaszmirem", "35% wiskoza, 30% poliamid, 30% merino, 5% kaszmir", "cena na stronie", "https://wolczanka.pl/granatowy-golf-damski-w%C3%B3lczanka-37434-1", False),
 "sw_welna": ("sweater", "#C9A27A", "Sweter wełniany", "Solar, wełniany sweter", "100% wełna owcza", "299 zł", "https://www.solar.com.pl/welniany-sweter-za035978.html", False),
 "zakiet_krem": ("boucle", "#EFE2CC", "Kremowy żakiet", "Ansin, Miss Jacket Creme", "100% wełna organiczna", "1 299 zł", "https://ansin.pl/kremowy-zakiet-welniany-damski", False),
 "bordo": ("blazer", "#7A2836", "Bordowa marynarka", "Ansin, Miss Heritage (melanż bordo)", "wełna z kaszmirem", "1 900 zł", "https://ansin.pl/welniana-marynarka-damska-melanzowe-bordo", False),
 "czolenka_gran": ("pumps", "#1E2A4A", "Granatowe czółenka STONE", "Kazar, niski obcas", "skóra naturalna", "ok. 549 zł", "https://kazar.com/granatowe-skorzane-czolenka-na-niskim-obcasie-60772-01-19.html", False),
 "kozaki": ("boots", "#6B4630", "Kozaki brązowe", "Ryłko (Zalando)", "skóra", "649,99 zł", "https://www.zalando.pl/rylko-rylko-kozaki-brown-ry111n0lr-o11.html", False),
 "mini": ("smallbag", "#B98A5A", "Mały kuferek ASHA S", "Kazar, 23×18 cm", "skóra naturalna", "799 zł", "https://kazar.com/jasnobrazowa-torebka-skorzana-typu-kuferek-90054-01-32.html", False),
 "jeans": ("jeans", "#5B7393", "Proste jeansy", "Reserved, straight leg YT188-55J", "denim", "99,99 zł", "https://www.reserved.com/pl/pl/jeansy-straight-leg-yt188-55j", False),
 "apaszka": ("scarf", "#2B3A66", "Apaszka", "Wólczanka, granatowa", "100% jedwab, 50×50 cm", "149,99 zł", "https://wolczanka.pl/granatowa-chusta-lambert-31404-1", False),
}

STYL = [
 ("Poniedziałek w biurze", "biuro", ["marynarka", "koszula", "spodnie_bez", "kuferek", "mokasyny"], "Spokojny granat i jedna mocna torebka."),
 ("Wystąpienie i konferencja", "scena", ["plaszcz", "sukienka", "perly", "czolenka"], "Jedna linia od szyi do kolan. Uwaga zostaje na twarzy."),
 ("Spotkanie z klientem", "klient", ["sw_blekit", "spodnica", "kuferek", "kozaki"], "Miękko, ale z kształtem."),
 ("Kolacja biznesowa", "wieczór", ["bordo", "jedwab", "spodnie_gran", "czolenka"], "Burgund wieczorem, jedwab przy twarzy."),
 ("Dzień przy kamerze", "online", ["sw_ecru", "perly", "spodnie_gran", "mokasyny"], "W kadrze widać górę – to ona ma błyszczeć."),
 ("Szkolenie całodniowe", "szkolenie", ["zakiet_krem", "golf", "spodnie_gran", "czolenka_gran"], "8 godzin na nogach, a nadal „ta, która prowadzi”."),
 ("Negocjacje i zarząd", "decyzje", ["zakiet_krem", "jedwab", "spodnie_bez", "kuferek", "mokasyny"], "Jasne kolory = przyszłam podejmować decyzje."),
 ("Networking po pracy", "event", ["plaszcz", "sukienka", "mini", "kozaki"], "Z biurka na event bez przebierania."),
 ("Podróż służbowa", "podróż", ["plaszcz", "sw_welna", "spodnie_gran", "mokasyny"], "Wszystko w bagażu podręcznym, nic się nie gniecie."),
 ("Weekend", "weekend", ["plaszcz", "sw_welna", "jeans", "apaszka", "mokasyny"], "Ta sama kapsuła, tylko luźniej."),
]
