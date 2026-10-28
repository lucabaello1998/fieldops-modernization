"""Genera migraciones Laravel a partir del esquema real del sistema original.

Entrada:  $ESQUEMA_TSV (por defecto ../privado/esquema-real.tsv)
          formato: tabla, columna, tipo, nullable, key
          El TSV no se versiona: describe el esquema de un sistema en produccion.
Salida:   database/migrations/2024_01_01_0000NN_create_<tabla>_table.php

Las vistas (vista_*) se omiten: se reimplementan como consultas/Eloquent.
"""
import os
import re
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TSV = os.environ.get("ESQUEMA_TSV", os.path.join(ROOT, "..", "privado", "esquema-real.tsv"))
OUT = os.path.join(ROOT, "database", "migrations")

SIMPLE = {
    "int": "integer", "bigint": "bigInteger", "smallint": "smallInteger",
    "tinyint": "tinyInteger", "mediumint": "mediumInteger",
    "float": "float", "double": "double", "decimal": "decimal",
    "date": "date", "datetime": "dateTime", "timestamp": "timestamp",
    "time": "time", "year": "year", "json": "json", "blob": "binary",
    "mediumblob": "mediumText", "longblob": "longText",
    "mediumtext": "mediumText", "longtext": "longText", "tinytext": "text",
    "text": "text", "bit": "boolean", "boolean": "boolean",
}


def col_definition(col, typ, nullable, key):
    """Devuelve (codigo_php, es_pk_auto)."""
    t = typ.lower()
    unsigned = "unsigned" in t
    base = re.sub(r"\s*unsigned\s*", "", t).strip()

    # auto-increment PK (int)
    if key == "PRI" and base in ("int", "bigint", "smallint", "tinyint"):
        method = {"int": "increments", "bigint": "bigIncrements",
                  "smallint": "smallIncrements", "tinyint": "tinyIncrements"}[base]
        return f"$table->{method}('{col}');", True

    m = re.match(r"^(varchar|char)\((\d+)\)$", base)
    if m:
        kind = "string" if m.group(1) == "varchar" else "char"
        line = f"$table->{kind}('{col}', {m.group(2)})"
    elif base.startswith("enum("):
        vals = re.findall(r"'((?:[^'\\]|\\.)*)'", typ)
        vals = ", ".join("'" + v.replace("'", "\\'").replace("\\'", "'") + "'" for v in vals)
        line = f"$table->enum('{col}', [{vals}])"
    elif base.startswith("decimal"):
        args = re.findall(r"\d+", typ)
        line = f"$table->decimal('{col}', {args[0]}, {args[1]})" if len(args) >= 2 else f"$table->decimal('{col}')"
    elif base.startswith("float"):
        args = re.findall(r"\d+", typ)
        line = f"$table->float('{col}', {args[0]}, {args[1]})" if len(args) >= 2 else f"$table->float('{col}')"
    elif base.startswith("double"):
        args = re.findall(r"\d+", typ)
        line = f"$table->double('{col}', {args[0]}, {args[1]})" if len(args) >= 2 else f"$table->double('{col}')"
    elif base in SIMPLE:
        line = f"$table->{SIMPLE[base]}('{col}')"
    else:
        line = f"$table->string('{col}')  /* tipo original: {typ} */"

    if unsigned and not line.startswith(("$table->increments", "$table->bigIncrements")):
        line += "->unsigned()"
    if nullable == "YES":
        line += "->nullable()"
    if key == "PRI":
        line += "->primary()"
    return line + ";", False


def row_bytes(cols, bpc=4):
    """Estimacion del tamano de fila InnoDB (utf8mb4 = 4 bytes por caracter)."""
    total = 0
    for _, typ, _, _ in cols:
        t = typ.lower()
        if t.startswith(("varchar", "char")):
            n = int(re.findall(r"\d+", t)[0])
            total += n * bpc + 1
        elif t.startswith("bigint"):
            total += 8
        elif t.startswith(("int", "mediumint")):
            total += 4
        elif t.startswith("smallint"):
            total += 2
        elif t.startswith(("tinyint", "year")):
            total += 1
        elif t.startswith(("text", "blob", "json")):
            total += 12
        elif t.startswith("datetime"):
            total += 5
        elif t.startswith("timestamp"):
            total += 4
        elif t.startswith(("date", "time")):
            total += 3
        else:
            total += 8
    return total


def main():
    if not os.path.exists(TSV):
        sys.exit(f"no encontre {TSV}")
    tables = {}
    order = []
    raw_lines = open(TSV, encoding="utf-8").read().splitlines()
    for raw in raw_lines[1:]:
        parts = re.split(r"[\t|]", raw)
        if len(parts) < 5:
            continue
        tabla, col, typ, nullable, key = parts[:5]
        if tabla not in tables:
            tables[tabla] = []
            order.append(tabla)
        tables[tabla].append((col, typ, nullable, key))

    os.makedirs(OUT, exist_ok=True)
    n = 0
    skipped = []
    for tabla in sorted(tables):
        if tabla.lower().startswith("vista_"):
            skipped.append(tabla)
            continue
        cols = tables[tabla]
        lines = []
        indexes = []
        for col, typ, nullable, key in cols:
            code, _ = col_definition(col, typ, nullable, key)
            lines.append("            " + code)
            if key == "MUL":
                indexes.append(f"            $table->index('{col}');")
            elif key == "UNI":
                indexes.append(f"            $table->unique('{col}');")
        body = "\n".join(lines + ([""] + indexes if indexes else []))
        n += 1
        fname = f"2024_01_01_{n:06d}_create_{tabla}_table.php"

        # Caso especial: si el nombre de una columna trae un punto, Laravel lo parte
        # como `tabla`.`columna`. Esa tabla se crea con SQL crudo para preservar el
        # nombre real del esquema legacy.
        if any("." in col for col, _, _, _ in cols):
            defs = []
            for col, typ, nullable, key in cols:
                d = f"  `{col}` {typ}"
                d += "" if nullable == "YES" else " NOT NULL"
                defs.append(d)
            sql = "CREATE TABLE `%s` (\n%s\n) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" % (
                tabla, ",\n".join(defs))
            php = f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

/**
 * Tabla legacy `{tabla}`: tiene columnas con nombres que el generador de esquema de
 * Laravel no puede escapar (por ejemplo `CANT. MAXIMA`, donde el punto se
 * interpretaria como separador tabla.columna). Se crea con SQL crudo tal cual el original.
 */
return new class extends Migration
{{
    public function up(): void
    {{
        DB::statement(<<<'SQL'
{sql}
SQL);
    }}

    public function down(): void
    {{
        DB::statement('DROP TABLE IF EXISTS `{tabla}`');
    }}
}};
"""
            with open(os.path.join(OUT, fname), "w", encoding="utf-8", newline="\n") as fh:
                fh.write(php)
            continue

        # InnoDB limita la fila a ~8126 bytes. El legacy tiene 33 tablas que lo exceden;
        # para replicarlas tal cual se crean con innodb_strict_mode=0 en la conexion.
        rb = row_bytes(cols)
        ancha = rb > 7800

        uses_db = ""
        comentario = (
            f"        // Tabla legacy ancha ({len(cols)} columnas, ~{rb} bytes de fila estimados):\n"
            "        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con\n"
            "        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.\n"
        ) if ancha else ""
        restaura = ""

        php = f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;
{uses_db}
return new class extends Migration
{{
    public function up(): void
    {{
{comentario}        Schema::create('{tabla}', function (Blueprint $table) {{
{body}
        }});
{restaura}    }}

    public function down(): void
    {{
        Schema::dropIfExists('{tabla}');
    }}
}};
"""
        with open(os.path.join(OUT, fname), "w", encoding="utf-8", newline="\n") as fh:
            fh.write(php)

    print(f"migraciones generadas: {n}")
    print(f"vistas omitidas ({len(skipped)}): {', '.join(sorted(skipped)[:6])}...")


if __name__ == "__main__":
    main()
