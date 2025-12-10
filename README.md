# JectStore

JectStore es una plataforma de comercio electrónico construida sobre Laravel 11 y Bagisto 2.3. Incluye todo lo necesario para levantar una tienda multi-idioma/multi-moneda con panel de administración, catálogo, carrito y pagos listos para extender.

## Tecnologías principales
- Laravel 11, PHP 8.2+, Composer 2
- Vue + Vite para los assets del frontend
- MySQL como base de datos principal (Redis y Elasticsearch opcionales)
- Arquitectura modular de Bagisto (`packages/Webkul/*`) para catálogo, clientes, ventas, marketing y más

## Requisitos
- PHP 8.2+ con extensiones: `intl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `curl`, `tokenizer`
- Composer 2
- Node.js 20+ y npm
- MySQL 8 (o MariaDB 10.5+), Redis opcional, Elasticsearch opcional
- Opcional: Docker + Docker Compose (Laravel Sail) si prefieres contenedores

## Puesta en marcha (entorno local)
1) Clona el repositorio y ubícate en la raíz del proyecto.  
2) Crea el archivo de entorno: `cp .env.example .env`.  
3) Ajusta variables clave en `.env`:
   - `APP_NAME=JectStore`, `APP_URL=http://localhost` y `APP_ADMIN_URL=admin` (ruta del panel).
   - Credenciales de base de datos (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
   - Configura correo (`MAIL_*`) y cache (`REDIS_*`) si aplica.
4) Instala dependencias de backend: `composer install`.  
5) Genera la clave de la app: `php artisan key:generate`.  
6) Crea el enlace de storage público: `php artisan storage:link`.  
7) Ejecuta el instalador interactivo de Bagisto/JectStore (migraciones + seed + usuario admin):  
   `php artisan bagisto:install --skip-github-star`  
   (si prefieres hacerlo manualmente: `php artisan migrate --seed` y crea el admin con `php artisan db:seed --class="Webkul\\Admin\\Database\\Seeders\\AdminSeeder"`).  
8) Instala dependencias de frontend: `npm install`.  
9) Levanta los assets en modo desarrollo: `npm run dev` (o construye para producción con `npm run build`).  
10) Sirve la aplicación: `php artisan serve` (o `php artisan octane:start` si deseas usar Octane).  
11) Accede al frontend en `APP_URL` y al panel en `APP_URL/APP_ADMIN_URL` con el usuario admin creado en el paso 7.

## Alternativa con Docker (Laravel Sail)
1) Copia `.env.example` a `.env` y define las credenciales (Sail usa por defecto `DB_USERNAME=sail` y `DB_PASSWORD=password`).  
2) Levanta los contenedores: `./vendor/bin/sail up -d`.  
3) Genera la clave: `./vendor/bin/sail artisan key:generate`.  
4) Corre el instalador: `./vendor/bin/sail artisan bagisto:install --skip-github-star`.  
5) Instala dependencias de frontend: `./vendor/bin/sail npm install`.  
6) Compila assets: `./vendor/bin/sail npm run dev` (o `npm run build`).  
7) Accede a `http://localhost` y al panel en `/APP_ADMIN_URL`.

## Comandos útiles
- `npm run dev` / `npm run build`: levantar HMR o generar assets minificados.
- `php artisan migrate --seed`: actualizar base de datos con datos de ejemplo.
- `php artisan queue:work`: procesar colas (pagos, notificaciones, etc.).
- `php artisan schedule:work`: ejecutar tareas programadas sin cron externo.
- `php artisan optimize:clear`: limpiar cachés si cambias configuración o vistas.

## Estructura breve
- `app/`: capa de aplicación Laravel.
- `packages/Webkul/*`: módulos de Bagisto (catálogo, checkout, ventas, marketing, etc.).
- `resources/`: vistas Blade, componentes Vue y assets manejados con Vite.
- `config/`: configuración de la plataforma y servicios.
- `docker-compose.yml`: stack Sail con MySQL, Redis, Elasticsearch, Kibana y Mailpit para desarrollo.
- `storage/`: archivos generados (logs, cachés, uploads). No se versionan.

## Despliegue
- Establece `APP_ENV=production` y `APP_DEBUG=false`.
- `composer install --no-dev --optimize-autoloader`.
- `npm ci && npm run build`.
- `php artisan migrate --force` y `php artisan storage:link`.
- Cachea configuración/rutas/vistas: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- Arranca un worker de colas (`php artisan queue:work --daemon`) y programa el scheduler con cron: `* * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1`.

## Recursos
- Documentación de Bagisto: https://devdocs.bagisto.com/
- Foros de la comunidad: https://forums.bagisto.com/

## Licencia
Proyecto basado en Bagisto y distribuido bajo licencia MIT. Consulta el archivo `LICENSE` para más detalles.
