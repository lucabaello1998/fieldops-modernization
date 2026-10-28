# FieldOps — modernización de un sistema legacy

Caso de estudio completo: un sistema de gestión de operaciones de campo (instalaciones de fibra
óptica, stock por depósito, técnicos, tareas/Kanban, reclamos e integración con SAP) escrito en
**PHP 7.4 + jQuery + MySQL**, sin framework, reescrito sobre **Laravel 12 + PHP 8.3 + MySQL 8**.

El punto de partida: 378 archivos PHP, 76 tablas sin claves foráneas y 11,3 millones de filas en la
tabla `tareas`, todo en producción. El trabajo cubre dos etapas: **reconstrucción del esquema real**
(76 tablas y 1.517 columnas) y **reescritura funcional**, conservando el esquema tal cual para que el
negocio no cambie su forma de trabajar.

- Sistema de diseño: [`docs/diseno.md`](docs/diseno.md) · Presentación: [`docs/FieldOps.pptx`](docs/FieldOps.pptx) (13 slides)
- Entorno y decisiones de infraestructura: [`docs/entorno.md`](docs/entorno.md)
- Demo y guion: [`DEMO.md`](DEMO.md)

## Correr la demo

```bash
cp .env.example .env                                           # configuración de demo lista (MySQL = servicio db)
docker compose up -d --build                                   # PHP 8.3 + Apache (8080), MySQL 8 (3307), Adminer (8081)
docker compose exec app php artisan migrate:fresh --seed       # 55 migraciones + 16 seeders sintéticos
npm install && npm run build                                   # Tailwind v4 + Alpine + Chart.js + SortableJS
```

Abrir **http://localhost:8080** y entrar con cualquiera de los usuarios de demo (contraseña
`password`, hasheada con bcrypt):

| Usuario | Rol | Qué ve |
|---|---|---|
| `admin@fieldops.test` | Administrador | Todo, incluido Usuarios |
| `supervisor@fieldops.test` | Supervisor | Tablero, tareas, altas, stock, técnicos, búsqueda |
| `tecnico@fieldops.test` | Técnico | Todo menos Usuarios |
| `deposito@fieldops.test` | Depósito | Ídem técnico, con foco en stock |

Los datos son **sintéticos** y deterministas (`mt_srand(20261023)`): mismo esquema y mismos
volúmenes que producción, cero datos de clientes. Guion paso a paso en [`DEMO.md`](DEMO.md).

## Cómo se prueba

Tres capas, todas contra la app real (nada de mocks de la base):

```bash
docker compose exec app php artisan test    # tests de feature sobre MySQL real
node tools/verificar.mjs                    # checks end-to-end con Chrome headless
node tools/capturar-todo.mjs                # capturas: cada pantalla en claro y oscuro
```

- **Tests de feature** (`tests/Feature/`): acceso y throttle del login, 403 por rol, cada pantalla
  con datos y con la base vacía, Kanban (alta, movimiento, nota, borrado), stock (entradas, salidas,
  stock que nunca queda negativo), usuarios (bcrypt, no borrarse a sí mismo, no quedarse sin
  administrador) y la búsqueda global.
- Corren sobre **MySQL real** (`fieldops_test`), no SQLite: las migraciones usan DDL propio del
  esquema legacy. `tests/TestCase.php` fija esa base antes de bootear la app, así la suite nunca
  toca los datos de la demo.
- **Verificación e2e** (`tools/verificar.mjs`): login, arrastre en el Kanban, alta de tarea,
  movimiento de stock, búsqueda y alta/baja de usuario, validando DOM y HTTP.
- **Pasada visual** (`tools/capturar-todo.mjs`): todas las pantallas, modales, 403 y 404, en los dos
  temas, para revisar la interfaz una por una.

## Qué trae la v2

| Módulo | Estado | Detalle |
|---|---|---|
| Login y roles | ✅ | Sesión de Laravel, contraseñas bcrypt, 4 roles, middleware `role:` |
| Tablero | ✅ | KPIs reales (altas del mes, tareas activas, stock crítico, técnicos) + 4 gráficos |
| Tareas (Kanban) | ✅ | 4 columnas, arrastrar y soltar con validación en el servidor, bitácora por tarea |
| Stock | ✅ | 205 líneas por depósito, niveles crítico/atención, movimientos internos auditados |
| Altas | ✅ | 640 instalaciones con filtros por zona, depósito, estado y fecha + ficha completa |
| Técnicos | ✅ | 142 técnicos, productividad de 30 días, equipos y zonas |
| Usuarios | ✅ | Alta, edición, baja y roles (solo Administrador) |
| Búsqueda global | ✅ | Tareas, altas, técnicos, materiales y reclamos en una sola consulta paginada |
| Modo claro / oscuro | ✅ | Toggle persistente, tokens de diseño compartidos |

Pendiente de migrar del legacy: ATC con mapas (Leaflet/KML), devolución de equipos, calendario de
recordatorios e integración SAP. El esquema de esas tablas ya está migrado, faltan las pantallas.

## Arquitectura

```
app/Http/Controllers/   8 controladores (uno por módulo)
app/Models/             55 modelos Eloquent, uno por tabla real del legacy (+ User)
app/Support/            DemoCatalog (catálogos sintéticos), helpers de presentación
database/migrations/    55 migraciones generadas desde el esquema real + las de Laravel
database/seeders/       16 seeders sintéticos deterministas
resources/views/        Blade: layouts app/guest, 6 componentes, 10 pantallas + errores con marca
resources/css/app.css   Tailwind v4 + tokens de diseño (--color-brand-*)
tests/Feature/          tests: acceso, roles, páginas, Kanban, stock, usuarios, búsqueda
tools/                  gen_migrations.py, gen_models.py, check_nombres.py,
                        verificar.mjs (e2e), capturar-todo.mjs (QA visual), presentacion/
```

Decisiones que vale la pena conocer:

- **El esquema real no se "arregló": se migró igual.** Las relaciones por nombre de texto
  (`altas.tecnico` → `tecnicos.tecnico`) se respetan, así los reportes siguen siendo comparables.
- **Nombres de columna raros y todo.** `final.'CANT. MAXIMA'` rompe el Blueprint de Laravel: esa
  tabla se crea con SQL crudo. Ver `tools/check_nombres.py`.
- **33 tablas legacy exceden el límite de fila de InnoDB** (8.126 bytes). La demo corre con
  `innodb-strict-mode=OFF` (ver `docker-compose.yml`) para replicar el esquema original sin
  recortarlo.
- **Sin dependencias de front raras**: Alpine + Chart.js + SortableJS sobre Vite, todo bundleado.

## El legacy que se reescribió

| Métrica | Valor |
|---|---|
| Archivos PHP propios | 378 (5,2 MB de código) |
| Tablas / columnas | 76 / 1.517 |
| Tabla más grande | `tareas` ≈ 11,3 M de filas |
| PHP en producción | 7.4.33 (sin soporte desde noviembre de 2022) |
| MySQL | 8.0.46 |
| Consultas SQL escritas a mano | 2.251 |
| Archivos en producción | 1.767 |

## Antes y después

| Aspecto | Sistema legacy | FieldOps |
|---|---|---|
| PHP | 7.4.33 (EOL) | 8.3.36 |
| Código | 378 archivos PHP + jQuery | Laravel 12 (MVC + Eloquent + Blade) |
| Datos | 2.251 consultas SQL a mano | Eloquent + PDO con parámetros enlazados |
| Front | jQuery disperso en cada vista | Vite + Alpine + Chart.js + Tailwind v4 |
| Estructura | 1.767 archivos sin capas | 10 controladores · 55 modelos · 19 vistas |
| Calidad | sin tests ni entorno reproducible | tests de feature + e2e + Docker |
| Puesta en marcha | FTP y cambios a mano en cPanel | `docker compose` + `migrate:fresh --seed` |

## Hoja de ruta

- [x] **Fase 0 — Relevamiento**: lectura del sistema en producción y reconstrucción del esquema
      real (76 tablas / 1.517 columnas).
- [x] **Fase 1 — Entorno reproducible**: Docker con PHP 8.3.36 + MySQL 8.0 + Adminer.
- [x] **Fase 2 — Esquema**: 55 migraciones y 55 modelos generados desde el esquema real;
      `migrate:fresh --seed` limpio.
- [x] **Fase 3 — Framework y UI**: Laravel 12 + Tailwind v4, 10 pantallas, verificación
      end-to-end contra la app real.
- [x] **Fase 4 — Calidad**: tests de feature sobre MySQL real, checks e2e y pasada visual de cada
      pantalla en claro y oscuro.
- [ ] **Fase 5 — Módulos restantes**: ATC con mapas, devoluciones, calendario e integración SAP.
- [ ] **Fase 6 — Despliegue**: pipeline de CI con la suite y migración de datos productivos.

## Aviso

Demo con **datos sintéticos**: mismo esquema y mismos volúmenes que el sistema original, cero datos
de clientes. Los volcados crudos y los materiales de análisis quedan fuera de git a propósito.
