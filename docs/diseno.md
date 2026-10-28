# Sistema de diseño — FieldOps (v2)

Estética **limpia, actual y sobria**: interfaz clara con barra lateral oscura, tarjetas blancas,
bordes suaves y un único color de acento. Nada de gradientes estridentes ni sombras pesadas.

## Tokens

| Token | Valor | Uso |
|---|---|---|
| Fuente | **Inter** (`Inter var`, fallback `system-ui`) | toda la app |
| Radio | `rounded-xl` (12px) tarjetas · `rounded-lg` (8px) controles | |
| Borde | `slate-200` (claro) · `slate-800` (oscuro) | 1px, sin sombras duras |
| Sombra | `shadow-sm` en reposo · `shadow-md` en hover | |
| Espaciado | base 4px, cards `p-5`/`p-6` | |
| Acento | **indigo-600** (`#4F46E5`) | botones, links, foco, gráficos |
| Fondo | `slate-50` (claro) · `slate-950` (oscuro) | |
| Superficie | `white` (claro) · `slate-900` (oscuro) | |
| Texto | `slate-900` / `slate-500` (claro) · `slate-100` / `slate-400` (oscuro) | |

## Estados semánticos

| Estado | Color | Uso |
|---|---|---|
| Éxito / activo | `emerald-500/600` | finalizado, stock ok |
| Aviso | `amber-500` | pendiente, stock bajo |
| Peligro | `rose-500` | crítico, baja, sin stock |
| Info | `sky-500` | en revisión, en tránsito |
| Neutro | `slate-400` | backlog, sin datos |

## Componentes

- **Sidebar** oscuro (`slate-900`) colapsable, ítem activo con barra indigo y fondo `slate-800`.
- **Topbar** con buscador global (⌘K), toggle de tema y menú de usuario.
- **KPI card**: label en mayúsculas `tracking-wide`, número grande `tabular-nums`, delta con flecha.
- **Data table**: header `slate-500 text-xs uppercase`, filas con hover `slate-50`, paginación Laravel.
- **Kanban**: columnas con header de color por estado, tarjetas arrastrables (SortableJS).
- **Charts**: Chart.js 4 — líneas suaves, sin gridline vertical, tooltip con borde redondeado.
- **Badges**: pill `rounded-full` con punto de color.
- **Empty state** y **toasts** para feedback.

## Movimiento

Transiciones de 150ms, `ease-out`. Hover eleva 1px + sombra. Sin animaciones largas.
