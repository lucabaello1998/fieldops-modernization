"""Genera modelos Eloquent 1:1 con el esquema real del sistema original.

Entrada: $ESQUEMA_TSV (por defecto ../privado/esquema-real.tsv, no versionado)
Salida:  app/Models/<Studly>.php

Reglas: $table exacto, $timestamps=false (el sistema original maneja 'cuando'/'quien'),
$guarded=[] para seeders, casts inferidos del tipo MySQL, PK segun el esquema.
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TSV = os.environ.get("ESQUEMA_TSV", os.path.join(ROOT, "..", "privado", "esquema-real.tsv"))
OUT = os.path.join(ROOT, "app", "Models")

CASTS = {
    "int": "integer", "bigint": "integer", "smallint": "integer", "tinyint": "integer",
    "date": "date", "datetime": "datetime", "timestamp": "datetime", "time": "string",
    "year": "integer",
}


def studly(name):
    parts = re.split(r"[^0-9A-Za-z]+", name)
    return "".join(p[:1].upper() + p[1:] for p in parts if p)


def singular(tabla):
    """Plural espanol -> singular (solo cambia el nombre de la clase, no la tabla)."""
    if tabla == "final":
        return "FinalRegistro"  # 'Final' es palabra reservada de PHP
    if tabla == "numeroSap":
        return tabla
    if tabla.endswith("es") and tabla[-3].lower() not in "aeiou":
        return tabla[:-2]
    if tabla.endswith("s") and not tabla.endswith("ss"):
        return tabla[:-1]
    return tabla


def main():
    rows = [re.split(r"[\t|]", l) for l in open(TSV, encoding="utf-8").read().splitlines()[1:] if l.strip()]
    rows = [r for r in rows if len(r) >= 5]
    tables = {}
    for t, c, typ, nullable, key in rows:
        tables.setdefault(t, []).append((c, typ, nullable, key))

    os.makedirs(OUT, exist_ok=True)
    made = 0
    for tabla in sorted(tables):
        if tabla.lower().startswith("vista_"):
            continue
        cols = tables[tabla]
        pk = next((c for c, _, _, k in cols if k == "PRI"), None)
        casts = {}
        for c, typ, _, _ in cols:
            base = re.sub(r"\(.*", "", typ.lower().replace(" unsigned", "")).strip()
            if base in CASTS:
                casts[c] = CASTS[base]
            elif base == "decimal":
                args = re.findall(r"\d+", typ)
                if len(args) >= 2:
                    casts[c] = f"decimal:{args[0]},{args[1]}"
        cast_lines = ",\n".join(f"            '{c}' => '{v}'" for c, v in casts.items())

        cls = studly(singular(tabla))
        php = f"""<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

/**
 * Tabla legacy `{tabla}` ({len(cols)} columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class {cls} extends Model
{{
    protected $table = '{tabla}';
    public $timestamps = false;
    protected $guarded = [];
"""
        if pk:
            php += f"    protected $primaryKey = '{pk}';\n"
        else:
            php += "    protected $primaryKey = null;\n    public $incrementing = false;\n"
        if cast_lines:
            php += f"""
    protected $casts = [
{cast_lines},
    ];
"""
        php += "}\n"

        with open(os.path.join(OUT, f"{cls}.php"), "w", encoding="utf-8", newline="\n") as fh:
            fh.write(php)
        made += 1

    print(f"modelos generados: {made}")
    print("ejemplos:", ", ".join(sorted(studly(singular(t)) for t in tables if not t.lower().startswith("vista_"))[:12]))


if __name__ == "__main__":
    main()
