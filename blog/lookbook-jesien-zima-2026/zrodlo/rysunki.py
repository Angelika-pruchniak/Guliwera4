"""Poprawione rysunki: długie rękawy, czytelne czółenko, wiszące perły (viewBox 200x240)."""
import math

INK = "#3A2340"


def _btn(x, y, r=4, c="#F4EFE6"):
    return f'<circle cx="{x}" cy="{y}" r="{r}" fill="{c}" stroke="{INK}" stroke-width="1.5"/>'


def _top(c, hem, flare=6, sw=2.5):
    ls = f'<path d="M64 32 L40 42 Q26 110 20 192 L44 196 Q52 120 62 78 Z" fill="{c}" stroke="{INK}" stroke-width="{sw}" stroke-linejoin="round"/>'
    rs = f'<path d="M136 32 L160 42 Q174 110 180 192 L156 196 Q148 120 138 78 Z" fill="{c}" stroke="{INK}" stroke-width="{sw}" stroke-linejoin="round"/>'
    body = f'<path d="M64 32 L136 32 L140 78 L{140+flare} {hem} L{60-flare} {hem} L60 78 Z" fill="{c}" stroke="{INK}" stroke-width="{sw}" stroke-linejoin="round"/>'
    cuffs = f'<path d="M21 182 L45 186 M155 186 L179 182" stroke="{INK}" stroke-width="2"/>'
    return ls + rs + body + cuffs


def coat(c, **k):
    return _top(c, 234, 14) + f'''<path d="M70 30 L86 92 L100 108 L114 92 L130 30" fill="{c}" stroke="{INK}" stroke-width="2"/><path d="M80 58 L92 66 M120 58 L108 66" stroke="{INK}" stroke-width="2"/>
<line x1="100" y1="108" x2="100" y2="234" stroke="{INK}" stroke-width="1.5"/>{_btn(90,128)}{_btn(110,128)}{_btn(90,160)}{_btn(110,160)}
<path d="M58 176 L78 176 M122 176 L142 176" stroke="{INK}" stroke-width="2"/>'''


def blazer(c, **k):
    return _top(c, 176, 4) + f'''<path d="M70 30 L84 82 L100 112 L116 82 L130 30" fill="{c}" stroke="{INK}" stroke-width="2"/><path d="M79 56 L91 66 M121 56 L109 66" stroke="{INK}" stroke-width="2"/>
<line x1="100" y1="112" x2="100" y2="176" stroke="{INK}" stroke-width="1.5"/>{_btn(100,128,5)}{_btn(100,152,5)}<path d="M62 150 L82 150 M118 150 L138 150" stroke="{INK}" stroke-width="2"/>'''


def boucle(c, **k):
    dots = "".join(f'<circle cx="{x}" cy="{y}" r="1.6" fill="{INK}" opacity=".22"/>' for x in range(24, 180, 8) for y in range(40, 192, 8))
    return _top(c, 172, 2) + dots + f'''<path d="M68 32 Q100 54 132 32" fill="none" stroke="{INK}" stroke-width="3"/><path d="M100 45 L100 172" stroke="{INK}" stroke-width="1.5"/>
{_btn(93,72,5)}{_btn(93,100,5)}{_btn(93,128,5)}{_btn(93,156,5)}<path d="M64 140 L84 140 M116 140 L136 140" stroke="{INK}" stroke-width="2.5"/><path d="M21 182 L45 186 M155 186 L179 182" stroke="{INK}" stroke-width="3"/>'''


def shirt(c, **k):
    return _top(c, 200, 2) + f'''<path d="M74 30 L88 58 L100 38 L112 58 L126 30" fill="{c}" stroke="{INK}" stroke-width="2"/><line x1="100" y1="38" x2="100" y2="200" stroke="{INK}" stroke-width="1.5"/>
{_btn(106,72,3,"#fff")}{_btn(106,102,3,"#fff")}{_btn(106,132,3,"#fff")}{_btn(106,162,3,"#fff")}'''


def blouse(c, **k):
    return _top(c, 195, 4) + f'''<path d="M100 46 Q82 38 74 54 Q86 64 100 50 Q114 64 126 54 Q118 38 100 46 Z" fill="{c}" stroke="{INK}" stroke-width="2"/><path d="M98 52 L88 96 M102 52 L112 92" stroke="{INK}" stroke-width="2"/>'''


def sweater(c, neck="crew", **k):
    if neck == "golf":
        collar = f'<path d="M74 14 L126 14 L128 38 Q100 46 72 38 Z" fill="{c}" stroke="{INK}" stroke-width="2"/>' + "".join(
            f'<line x1="{x}" y1="16" x2="{x}" y2="38" stroke="{INK}" stroke-width="1" opacity=".5"/>' for x in range(80, 124, 6))
    else:
        collar = f'<path d="M74 32 Q100 50 126 32" fill="none" stroke="{INK}" stroke-width="3"/>'
    rib = "".join(f'<line x1="{x}" y1="184" x2="{x}" y2="196" stroke="{INK}" stroke-width="1"/>' for x in range(58, 144, 7))
    return _top(c, 196, 0) + collar + f'<path d="M60 184 L140 184" stroke="{INK}" stroke-width="2"/>' + rib


def golf(c, **k):
    return sweater(c, neck="golf")


def pumps(c, **k):
    return f'''<g transform="translate(0,70)"><path d="M14 112 Q20 92 52 90 Q96 88 128 64 Q140 54 160 54 Q178 56 180 74 L178 86 L170 88 L168 148 L162 148 L158 92 Q130 100 108 112 Q70 128 22 124 Q12 122 14 112 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M14 118 Q60 128 108 112" fill="none" stroke="{INK}" stroke-width="3"/></g>'''


def pearls(c, **k):
    pts = "".join(f'<circle cx="{100+70*math.sin(t):.1f}" cy="{50+150*(1-math.cos(t))/2:.1f}" r="7.5" fill="#F7F2EA" stroke="{INK}" stroke-width="1.3"/>'
                  for t in [-math.pi * 0.95 + i * (math.pi * 1.9 / 30) for i in range(31)])
    return pts
