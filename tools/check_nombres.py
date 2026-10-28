"""Reporte de nombres de tabla/columna del esquema original que rompen al generar DDL.

Uso: python tools/check_nombres.py   (lee $ESQUEMA_TSV, no versionado)
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TSV = os.environ.get("ESQUEMA_TSV", os.path.join(ROOT, "..", "privado", "esquema-real.tsv"))

rows = [re.split(r"[\t|]", l) for l in open(TSV, encoding="utf-8").read().splitlines()[1:] if l.strip()]
rows = [r for r in rows if len(r) >= 5 and not r[0].lower().startswith("vista_")]

trampas = [(t, c) for t, c, _, _, _ in rows if any(ch in c for ch in '.`"\\\'')]
espacios = [(t, c) for t, c, _, _, _ in rows if " " in c]
acentos = [(t, c) for t, c, _, _, _ in rows if not c.isascii()]
tablas_espacio = sorted({t for t, _, _, _, _ in rows if " " in t})

print(f"columnas con . ` \" \\ '  : {len(trampas)}")
for t, c in trampas:
    print(f"    {t}.{c!r}")

print(f"\ncolumnas con espacios      : {len(espacios)}")
for t, c in espacios:
    print(f"    {t}.{c!r}")

print(f"\ncolumnas no-ASCII          : {len(acentos)}")
for t, c in acentos:
    print(f"    {t}.{c!r}")

print(f"\ntablas con espacios        : {tablas_espacio}")
print(f"\ntotal columnas (sin vistas): {len(rows)}")
