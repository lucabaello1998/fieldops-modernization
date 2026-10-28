# Demo — FieldOps

Guion para mostrar la aplicación funcionando, más la verificación automática que corre contra ella.

## 1. Levantar

```bash
cp .env.example .env          # configuración de demo lista (MySQL = servicio db)
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
npm install && npm run build
```

| Servicio | URL | Credenciales |
|---|---|---|
| App | http://localhost:8080 | ver tabla de abajo |
| Adminer | http://localhost:8081 | servidor `db`, usuario `fieldops`, contraseña `secret` |
| MySQL | `localhost:3307` | ídem |

`migrate:fresh --seed` es determinista (`mt_srand(20261023)`): volver a correrlo deja exactamente
los mismos datos. Para volver a un estado limpio entre ensayos, alcanza con repetirlo.

## 2. Usuarios

Contraseña para todos: `password` (guardada con bcrypt, nunca en claro).

| Usuario | Rol | Alcance |
|---|---|---|
| `admin@fieldops.test` | Administrador | Todo |
| `supervisor@fieldops.test` | Supervisor | Sin Usuarios |
| `tecnico@fieldops.test` | Técnico | Ídem |
| `deposito@fieldops.test` | Depósito | Ídem |

## 3. Guion (10 minutos)

**1 · Login** — `/login` *(30 s)*
Pantalla partida, validación del lado del servidor y `throttle:6,1`. El usuario se crea y la columna
ya guarda bcrypt: la contraseña nunca viaja ni se guarda en claro.

**2 · Tablero** — `/` *(1 min)*
KPIs y cuatro gráficos construidos sobre las tablas reales: altas del mes vs. mes anterior,
producción mensual, stock por depósito, tareas por estado. Aclarar que los números salen de
`tareas`, `altas`, `materiales` y `produccion` del esquema migrado, no de tablas nuevas.

**3 · Modo oscuro** — botón de tema, arriba a la derecha *(15 s)*
Un clic, queda persistido. Sirve para mostrar que hay un sistema de diseño detrás
([`docs/diseno.md`](docs/diseno.md)) y no una plantilla.

**4 · Tareas (Kanban)** — `/tareas` *(2 min)*
Arrastrar una tarjeta de **Pendiente** a **En curso**. Abrir la tarea: la bitácora ya registra
"*movió la tarea de Pendiente a En curso*" con usuario y fecha. Después, **Nueva tarea**: el modal
permite tipo, prioridad, responsable y vencimiento.

**5 · Stock** — `/stock` *(1 min)*
205 líneas por depósito con tres niveles (crítico ≤ 15, atención ≤ 40). Registrar un movimiento en
el formulario de la derecha: entra como movimiento interno con usuario y fecha, y el listado se
actualiza. El filtro de la izquierda permite ver sólo los críticos.

**6 · Altas** — `/altas` *(1 min)*
640 instalaciones con filtros por zona, depósito, estado y rango de fechas. Abrir una ficha
(`/altas/1`): equipos, potencias, dirección, técnico asignado. Mostrar que la relación
`altas.tecnico → tecnicos.tecnico` sigue siendo por texto: es el mismo dato que ve el negocio hoy.

**7 · Búsqueda global** — `/buscar?q=aguirre` *(1 min)*
Un solo endpoint busca en tareas, altas, técnicos, materiales y reclamos, resaltando las
coincidencias y agrupando por tipo de registro. El término viaja como parámetro enlazado: no hay
SQL que armar a mano.

**8 · Usuarios** — `/usuarios` *(1 min, solo Administrador)*
Alta de un usuario con rol, edición y baja. El sistema no deja borrarse a sí mismo ni quedarse sin
administrador; las reglas están cubiertas por tests.

**9 · Cómo se hizo** *(1 min)*
76 tablas migradas sin recortes (55 migraciones y 55 modelos generados desde el esquema real),
16 seeders sintéticos y 21 rutas de aplicación: 19 exigen sesión y 4 además rol de Administrador.
Un detalle que suele gustar: la columna `final.'CANT. MAXIMA'` rompe el generador de DDL de
Laravel, así que esa tabla se crea con SQL crudo.

**10 · Cierre** *(30 s)*
Lo que sigue: módulos ATC/mapas, devoluciones, calendario, integración SAP y despliegue con CI.

## 4. Verificación automática

**Tests de feature** contra MySQL real, en una base aparte (`fieldops_test`) que la suite nunca
mezcla con los datos de la demo:

```bash
docker compose exec app php artisan test
```

Cubren: login válido/inválido/inactivo, throttle a los 6 intentos, logout, 403 por rol, cada
pantalla con datos y con la base vacía, Kanban (alta, movimiento con bitácora, nota, borrado),
stock (entradas, salidas, stock que nunca queda negativo), usuarios (bcrypt, no borrarse a sí
mismo, no quedarse sin administrador) y la búsqueda global.

**Verificación e2e**: `tools/verificar.mjs` maneja la app real con Chrome headless: hace login,
arrastra una tarjeta, crea una tarea, registra un movimiento de stock, busca, crea y borra un
usuario. Cada paso se valida contra el DOM y las respuestas HTTP.

```bash
npm install                      # incluye puppeteer-core (dev)
node tools/verificar.mjs         # con la app levantada en :8080
```

## 5. Capturas y presentación

Las capturas de `docs/capturas/deck/` son de la app corriendo de verdad (no maquetas), tomadas con
Chrome headless a 1600×1000 en escala 1,5:

```bash
node tools/presentacion/capturar.mjs     # regenera docs/capturas/deck/*.png
node tools/presentacion/build.mjs        # regenera docs/FieldOps.pptx (13 slides)
node tools/capturar-todo.mjs             # QA visual: cada pantalla en claro y oscuro
```

## 6. Si algo falla

| Síntoma | Causa | Solución |
|---|---|---|
| `Permission denied` en `storage/logs` | `composer` corrió como root y dejó archivos con otro dueño | `docker compose exec app chown -R www-data:www-data storage bootstrap/cache` |
| La página se ve sin estilos o con estilos viejos | `public/build` desactualizado respecto de las vistas | `npm run build` |
| `Row size too large` al migrar | falta `--innodb-strict-mode=OFF` en el servicio `db` | revisar `docker-compose.yml` |
| Puerto 8080 / 3307 ocupado | otro contenedor o servicio local | cambiar el mapeo en `docker-compose.yml` |

## 7. Pendientes

- Módulos del legacy sin pantalla en v2: ATC con mapas, devolución de equipos, calendario de
  recordatorios, integración SAP.
- Pipeline de CI que corra `php artisan test` y `tools/verificar.mjs` en cada push, y despliegue a
  un entorno de staging.
