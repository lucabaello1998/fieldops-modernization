# Deploy de la demo

La app ya tiene una imagen de producción (`docker/php/Dockerfile.prod`) que:

- compila los assets con Vite en una etapa aparte,
- instala `vendor` sin dependencias de desarrollo,
- cachea config, rutas y vistas al arrancar,
- corre `php artisan migrate --force` (y opcionalmente los seeders) esperando a que la base esté.

Verificada localmente: `docker build -f docker/php/Dockerfile.prod -t fieldops:prod .` y luego
levantarla contra MySQL devuelve `200` en `/login` con los assets compilados.

## Variables de entorno

| Variable | Valor |
|---|---|
| `APP_KEY` | `base64:...` (generar con el comando de abajo) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | la URL pública del deploy |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` | host y puerto del MySQL gestionado |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | credenciales de ese MySQL |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `sync` |
| `RUN_MIGRATIONS` | `1` (migra al arrancar) |
| `RUN_SEED` | `1` la primera vez (datos sintéticos), después `0` |

Generar la key sin instalar PHP:

```bash
docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

> Los datos de la demo son sintéticos y deterministas. No hay datos de clientes en el repo ni en la
> base: se puede publicar sin riesgo.

## Opción A — Railway (recomendada: trae MySQL)

1. [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo** →
   `lucabaello1998/fieldops-modernization`. Railway detecta `railway.json` y usa la imagen de
   producción.
2. En el mismo proyecto: **New** → **Database** → **MySQL**.
3. En el servicio de la app, **Variables**:

   ```env
   APP_KEY=base64:...
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://<tu-dominio>.up.railway.app
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=sync
   RUN_MIGRATIONS=1
   RUN_SEED=1
   ```

4. **Settings** → **Networking** → **Generate Domain**.
5. Esperar el deploy: el entrypoint migra y siembra solo. Entrar con `admin@fieldops.test` /
   `password`.

## Opción B — Fly.io

Fly no da MySQL, así que hace falta una base externa gratuita (Aiven, TiDB Cloud, Clever Cloud).

```bash
fly auth signup
fly launch --no-deploy --dockerfile docker/php/Dockerfile.prod
fly secrets set APP_KEY="base64:..." APP_URL="https://fieldops-demo.fly.dev" \
  DB_CONNECTION=mysql DB_HOST="..." DB_PORT="3306" \
  DB_DATABASE="..." DB_USERNAME="..." DB_PASSWORD="..."
fly deploy
```

El `fly.toml` del repo ya define el healthcheck contra `/login`, el escalado a cero y las variables
de sesión/caché.

## Automatizar el deploy desde GitHub

`.github/workflows/deploy.yml` despliega en Fly cuando se corre a mano o cuando se publica un tag
`v*`, siempre que exista el secreto `FLY_API_TOKEN`:

```bash
gh secret set FLY_API_TOKEN --repo lucabaello1998/fieldops-modernization
```

Sin el secreto, el workflow se saltea el deploy (no falla).

## Checklist después del primer deploy

- [ ] `/login` responde 200 y carga estilos (Vite compilado).
- [ ] El login con `admin@fieldops.test` / `password` entra al tablero.
- [ ] El Kanban permite arrastrar una tarjeta y queda registrada en la bitácora.
- [ ] `/usuarios` lista los usuarios sembrados.
- [ ] Cambiar `RUN_SEED` a `0` para no volver a sembrar en cada arranque.
