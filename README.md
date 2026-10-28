# Overskull – API REST (Laravel 11)

API REST para la gestión de **productos** y **categorías** (CRUD completo), con respuestas JSON estandarizadas, validaciones, documentación Swagger, auditoría de acciones y observabilidad con Laravel Telescope.

Frontend: [overskull-vue-frontend](https://github.com/GeGuMaGu25/overskull-vue-frontend)

## Tecnologías

| Herramienta | Uso |
|---|---|
| Laravel 11 | Framework backend |
| MySQL 8 | Base de datos |
| darkaonline/l5-swagger (swagger-php) | Documentación OpenAPI en `/api/docs` |
| Laravel Telescope | Observabilidad (requests, queries, excepciones) |
| spatie/laravel-activitylog | Auditoría de create / update / delete |
| PHPUnit | Pruebas de feature |
| Docker (opcional) | PHP-FPM + Nginx + MySQL |

## Requisitos previos

- PHP **8.2** o superior con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` (y `pdo_sqlite` para correr los tests)
- Composer 2
- MySQL 8 (o MariaDB 10.6+)
- Git

> También puedes levantar todo con Docker sin instalar PHP ni MySQL (ver más abajo).

## Instalación local

```bash
git clone https://github.com/GeGuMaGu25/overskull-laravel-api.git
cd overskull-laravel-api

composer install
cp .env.example .env          # en Windows (PowerShell): copy .env.example .env
php artisan key:generate
```

Crea la base de datos vacía `overskull_db` en MySQL y revisa las credenciales en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=overskull_db
DB_USERNAME=root
DB_PASSWORD=
```

Ejecuta las migraciones con datos de ejemplo y levanta el servidor:

```bash
php artisan migrate --seed
php artisan serve
```

## URLs base

| Recurso | URL |
|---|---|
| API | http://localhost:8000/api |
| Documentación Swagger | http://localhost:8000/api/docs |
| JSON OpenAPI | http://localhost:8000/docs |
| Laravel Telescope | http://localhost:8000/telescope |

## Endpoints

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/categories` | Listar categorías (incluye `products_count`) |
| POST | `/api/categories` | Crear categoría |
| GET | `/api/categories/{id}` | Ver categoría |
| PUT/PATCH | `/api/categories/{id}` | Actualizar categoría |
| DELETE | `/api/categories/{id}` | Eliminar categoría (409 si tiene productos) |
| GET | `/api/products` | Listar productos (`?search=` y `?category_id=`) |
| POST | `/api/products` | Crear producto |
| GET | `/api/products/{id}` | Ver producto |
| PUT/PATCH | `/api/products/{id}` | Actualizar producto |
| DELETE | `/api/products/{id}` | Eliminar producto |
| GET | `/api/activity-logs` | Log de auditoría (`?log_name=product\|category`, `?event=`, `?limit=`) |

Ejemplo de cuerpo para crear un producto:

```json
{
  "name": "Camiseta Overskull",
  "description": "Camiseta negra edición limitada",
  "price": 89.90,
  "stock": 50,
  "category_id": 1
}
```

## Formato de respuesta estándar

Respuesta exitosa:

```json
{
  "success": true,
  "message": "Producto creado correctamente",
  "data": { "id": 1, "name": "Camiseta Overskull", "price": 89.9 }
}
```

Respuesta de error:

```json
{
  "success": false,
  "message": "El campo nombre es requerido",
  "errors": { "name": ["El campo nombre es requerido"] }
}
```

## Manejo de errores

Todas las excepciones de las rutas `api/*` pasan por `App\Exceptions\ApiExceptionRenderer` (registrado en `bootstrap/app.php`) y se devuelven con el formato estándar:

| Código | Caso |
|---|---|
| 422 | Error de validación (`errors` contiene el detalle por campo) |
| 404 | Recurso o ruta inexistente |
| 405 | Método HTTP no permitido |
| 409 | Conflicto (por ejemplo, eliminar una categoría con productos) |
| 500 | Error interno (el detalle solo se muestra con `APP_DEBUG=true`) |

Además, el middleware `ForceJsonResponse` obliga a que las rutas de la API respondan siempre en JSON aunque el cliente no envíe `Accept: application/json`.

## Auditoría

Los modelos `Product` y `Category` usan el trait `LogsActivity` de Spatie, por lo que cada creación, actualización y eliminación queda registrada en la tabla `activity_log` con los valores anteriores y nuevos. Se pueden consultar desde `GET /api/activity-logs`.

## Telescope

Está habilitado en entorno `local` (`TELESCOPE_ENABLED=true`). Después de hacer algunas peticiones a la API, entra a `/telescope` para ver requests, queries SQL, modelos y excepciones.

## Documentación Swagger

Los endpoints se documentan con atributos de `swagger-php` directamente en los controladores, y los esquemas reutilizables están en `app/OpenApi/OpenApiSpec.php`. Con `L5_SWAGGER_GENERATE_ALWAYS=true` la documentación se regenera en cada carga; también puede generarse manualmente:

```bash
php artisan l5-swagger:generate
```

## Estructura del proyecto

```
app/
├─ Exceptions/
│  ├─ ApiExceptionRenderer.php        # Errores → JSON estándar
│  └─ CategoryHasProductsException.php
├─ Http/
│  ├─ Controllers/Api/                # Controladores delgados (solo HTTP)
│  ├─ Middleware/ForceJsonResponse.php
│  ├─ Requests/                       # Validaciones (Form Requests)
│  └─ Resources/                      # Transformación de modelos a JSON
├─ Models/                            # Category, Product (+ auditoría)
├─ OpenApi/OpenApiSpec.php            # Info y esquemas de Swagger
├─ Services/                          # Lógica de negocio
└─ Support/ApiResponse.php            # Constructor del formato de respuesta
database/
├─ factories/
├─ migrations/
└─ seeders/                           # CategorySeeder, ProductSeeder
routes/api.php
tests/Feature/                        # Pruebas de la API
docker/                               # Configuración de Nginx y PHP
docker-compose.yml
```

Cada capa tiene una sola responsabilidad (SRP): el **Form Request** valida, el **Service** aplica la lógica de negocio, el **Resource** da forma a la salida, `ApiResponse` construye el sobre JSON y el **Controller** solo orquesta.

## Pruebas

```bash
php artisan test
```

Las pruebas usan SQLite en memoria, por lo que no tocan tu base de datos MySQL.

## Docker (opcional)

Levanta PHP-FPM, Nginx y MySQL:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

La API queda disponible en http://localhost:8000 y MySQL se expone en el puerto `3307` (usuario `overskull`, contraseña `secret`). Las variables de conexión del contenedor se definen en `docker-compose.yml` y tienen prioridad sobre el `.env`.

Para detener los servicios:

```bash
docker compose down
```

## Flujo de trabajo Git

El repositorio sigue **Git Flow** (`main`, `develop`, `feature/*`, `release/*`) y **Conventional Commits** (`feat`, `fix`, `docs`, `test`, `chore`, `build`, `refactor`).
