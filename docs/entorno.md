# Entorno local

## Requisitos

- Docker Desktop con el motor WSL2 corriendo.
- Node 20+ y npm (solo para compilar los assets y correr los scripts de verificación).
- Opcional: Google Chrome, para las capturas y la verificación end-to-end.

No hace falta PHP, Composer ni MySQL en el host: todo corre dentro de los contenedores.

## Levantar

```bash
cp .env.example .env          # ya viene configurado para la demo
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
npm install && npm run build
```

| Servicio | URL / puerto |
|---|---|
| App (Apache + PHP 8.3.36) | http://localhost:8080 |
| Adminer (cliente MySQL) | http://localhost:8081 |
| MySQL 8.0 | localhost:3307 · usuario `fieldops` · contraseña `secret` |

El proyecto de Compose se llama `fieldops` (`name:` en `docker-compose.yml`), así que los
contenedores y el volumen son estables: `fieldops-app`, `fieldops-db`, `fieldops-adminer`,
`fieldops_dbdata`.

## Base de datos

- La base de la demo es `fieldops`; la suite de tests usa `fieldops_test`, que se crea una vez con
  permisos para el mismo usuario:

```bash
docker compose exec db mysql -uroot -proot -e "
  CREATE DATABASE IF NOT EXISTS fieldops_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  GRANT ALL PRIVILEGES ON fieldops_test.* TO 'fieldops'@'%';
  FLUSH PRIVILEGES;"
```

- `php artisan test` migra `fieldops_test` desde cero (`RefreshDatabase`). `tests/TestCase.php`
  fuerza esa base antes de bootear la app, así que la suite nunca toca los datos de la demo.
- El servidor arranca con `--innodb-strict-mode=OFF`: 33 tablas del esquema legacy superan el
  límite de fila de InnoDB (8.126 bytes) y se migran tal cual, sin recortar columnas.

## Recrear todo desde cero

```bash
docker compose down -v        # borra el volumen de MySQL
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
```
