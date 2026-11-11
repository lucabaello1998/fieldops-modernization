-- La suite de PHPUnit corre contra una base aparte: phpunit.xml fija fieldops_test.
-- MySQL crea fieldops (la de la demo) automaticamente; esta faltaba y hacia que
-- `docker compose exec app php artisan test` fallara con "Unknown database fieldops_test".
CREATE DATABASE IF NOT EXISTS fieldops_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON fieldops_test.* TO 'fieldops'@'%';

FLUSH PRIVILEGES;
