"""Wstawia twarde spacje: liczby (9 900 zł), jednoliterowe spójniki i krótkie przyimki. Działa tylko na tekście poza tagami."""
import re, sys
p = sys.argv[1]; h = open(p, encoding='utf-8').read()
head, body = h.split('<body>', 1)
def fix(s):
    for _ in range(2): s = re.sub(r'(\d) (\d{3})', '\\1\u00a0\\2', s)
    s = re.sub(r'(\d) (zł|dni|h\b|min|mies|osób|tys|mln|godzin|minut|klientów|rozmów|umowy|priorytety|liczby|wpisów|rund|fotele|stron)', '\\1\u00a0\\2', s)
    s = re.sub(r'(^|[\s(„])(i|w|z|o|a|u|na|do|od|po|bez|dla|się|ze|we|nie) ', lambda m: m.group(1)+m.group(2)+'\u00a0', s)
    return s
parts = re.split(r'(<[^>]+>)', body)
body = ''.join(x if x.startswith('<') else fix(x) for x in parts)
open(p, 'w', encoding='utf-8').write(head + '<body>' + body)
