# Urbano Express Orders

Implementacion del challenge tecnico de Urbano Express usando Laravel 11, Vue 3 y Docker.

## Alcance

- API REST con `POST /api/orders`, `GET /api/orders` y `GET /api/orders/{id}`.
- Persistencia en SQLite.
- Autenticacion Bearer token para la API.
- Logging de creacion de pedidos en `storage/logs/laravel.log`.
- Interfaz Vue 3 para cargar pedidos y ver el listado en tiempo real.
- Tests de API con PHPUnit.
- Dockerfile y `docker-compose.yml` para levantar el entorno.

## Modelo de pedido

```json
{
  "external_id": "shopify-1001",
  "customer_name": "Ada Lovelace",
  "customer_email": "ada@example.com",
  "shipping_address": "Calle Falsa 123, Buenos Aires",
  "status": "pending",
  "currency": "ARS",
  "total_amount": 14999.9,
  "items": [
    {
      "sku": "SKU-01",
      "name": "Caja Urbana",
      "quantity": 2,
      "price": 7499.95
    }
  ]
}
```

## Ejecucion local

```bash
cp .env.example .env
php artisan migrate
php artisan serve
```

Aplicacion web: `http://127.0.0.1:8000`

Token Bearer por defecto: `urbano-demo-token`

Si tu PHP local no tiene `pdo_sqlite` habilitado globalmente pero el modulo ya esta instalado, podes correr:

```bash
composer run migrate:sqlite
composer run test:sqlite
```

Para dejarlo habilitado de forma permanente en este host falta crear `/etc/php/conf.d/sqlite.ini` con:

```ini
extension=sqlite3
extension=pdo_sqlite
```

## Docker

```bash
docker compose up --build
```

La app queda disponible en `http://localhost:8000`.

## Endpoints

### Crear pedido

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Accept: application/json" \
  -H "Authorization: Bearer urbano-demo-token" \
  -H "Content-Type: application/json" \
  -d '{
    "external_id": "shopify-1001",
    "customer_name": "Ada Lovelace",
    "customer_email": "ada@example.com",
    "shipping_address": "Calle Falsa 123, Buenos Aires",
    "status": "pending",
    "currency": "ARS",
    "total_amount": 14999.9,
    "items": [
      {
        "sku": "SKU-01",
        "name": "Caja Urbana",
        "quantity": 2,
        "price": 7499.95
      }
    ]
  }'
```

### Listar pedidos

```bash
curl http://127.0.0.1:8000/api/orders \
  -H "Accept: application/json" \
  -H "Authorization: Bearer urbano-demo-token"
```

### Obtener un pedido

```bash
curl http://127.0.0.1:8000/api/orders/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer urbano-demo-token"
```

## Tests

```bash
php artisan test
```

## Estructura principal

- `app/Http/Controllers/Api/OrderController.php`: endpoints REST de pedidos.
- `app/Http/Middleware/OrderTokenMiddleware.php`: validacion del token Bearer.
- `app/Models/Order.php`: modelo Eloquent.
- `database/migrations/2026_05_07_000000_create_orders_table.php`: esquema de la tabla `orders`.
- `resources/views/orders/index.blade.php`: interfaz Vue 3 embebida.
- `routes/api.php`: rutas de la API.
- `routes/web.php`: pantalla principal.
