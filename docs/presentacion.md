# Guion de la presentación — FieldOps

Formato objetivo: `.pptx` (16:9). Estética: igual al sistema de diseño (`docs/diseno.md`) —
fondo claro, tipografía Inter, acento indigo, mucho aire, datos en tipografía tabular.

Generación: `node tools/presentacion/build.mjs` → `docs/FieldOps.pptx` (13 slides).

## Slides

1. **Portada** — "FieldOps" · *Un sistema de gestión de campo, reescrito sobre Laravel*.
   Nombre, fecha, stack. Composición limpia, una sola línea de acento.
2. **El punto de partida** — métricas del legacy en una fila de 4 números grandes:
   378 archivos PHP · 76 tablas / 1.517 columnas · 11,3M filas · PHP 7.4 (EOL 2022).
   Cierre: "un sistema que creció sin planos".
3. **La decisión: no parchear, reescribir** — tres tarjetas (parchear, migrar de versión,
   reescribir sobre Laravel 12, la elegida). Abajo: el esquema real se conserva y los chips del
   stack.
4. **Antes y después** — tabla de 7 filas: PHP, código, datos, front, estructura, calidad y puesta
   en marcha.
5. **La nueva aplicación** — captura del tablero (KPIs + 4 gráficos) con datos del esquema migrado.
6. **Modo oscuro** — misma pantalla, tokens compartidos; el tema persiste.
7. **Tareas: tablero Kanban** — arrastrar y soltar con validación del lado del servidor, más el
   alta de tareas en modal.
8. **Cada cambio queda registrado** — la bitácora de la tarea: quién, cuándo y desde qué estado.
9. **Stock y altas** — los dos módulos de operación diaria, con capturas.
10. **Búsqueda global** — un solo endpoint parametrizado para tareas, altas, técnicos, materiales y
    reclamos.
11. **Cómo se verifica** — 53 tests de feature · 10 checks e2e · 33 capturas visuales · 0 errores.
12. **Correr la demo** — 4 comandos, URL, Adminer y usuarios de demo.
13. **Próximos pasos** — módulos restantes, integración SAP, despliegue con CI y migración de datos
    reales.

## Reglas de diseño del deck

- Máximo ~35 palabras por slide; el número es el protagonista.
- Una idea por slide; sin bullets anidados.
- Tablas solo en el slide 4 (antes/después).
- Capturas reales (Chrome headless) con marco redondeado y sombra suave.
