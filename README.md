# Webcaps

Tienda online desarrollada en PHP con panel administrativo para gestionar productos, categorias y pedidos.

## Estado actual

- Base funcional de e-commerce (catalogo, carrito, checkout, panel admin).
- UI modernizada con **TailwindCSS** en todas las paginas publicas y del admin.
- **Skeleton loaders** y **lazy loading** en el catalogo de productos.
- **Toast notifications** reemplazan alerts del carrito.
- **Dark mode** toggle persistente con `localStorage`.
- **Búsqueda full-text** en tiempo real en el catalogo publico.
- Mejoras de seguridad aplicadas: CSRF en login y panel, XSS sanitizacion, manejo de sesiones seguro.
- Helpers de robustez: `Auth`, `Csrf`, `Database`, `Validator`.
- Rutas absolutas con `__DIR__` en todo el panel y public proxies corregidos.
- Stock se descuenta automaticamente al completar pedido.
- Correo unificado por pedido (no por producto).
- Front Controller con punto de entrada unico en `public/`.
- Estructura reorganizada con separacion de responsabilidades.

## Features de portafolio

| Feature | Descripcion | Archivos clave |
|---------|-------------|----------------|
| **Seeder** | Genera 20 productos, 10 pedidos y noticias de prueba | `database/seeder.php` |
| **PDF Export** | Descarga orden de pedido en PDF desde el admin | `panel/pedidos/pdf.php` |
| **API REST** | Endpoints JSON para productos y categorias | `api/productos.php`, `api/categorias.php` |
| **Dashboard** | Metricas en cards + grafica Chart.js de ventas | `panel/productos/index.php` |
| **Tests** | PHPUnit para helpers Csrf y Validator | `tests/Helpers/*` |
| **Type Hints** | Tipado en modelos Productos, Pedidos, Categorias | `src/Models/*.php` |

## Stack recomendado (local)

| Herramienta | Funcion |
|-------------|---------|
| **DDEV** | Entorno Docker todo-en-uno (PHP, Nginx, MariaDB, Mailpit). Reemplaza XAMPP. |
| **Composer 2.x** | Gestor de dependencias PHP. |
| **PHP 8.2+** | Requerido por las dependencias actuales. |

### Por que DDEV en vez de XAMPP

- **Un comando**: `ddev start` levanta todo el stack.
- **Mailpit incluido**: captura emails sin enviarlos a produccion.
- **Base de datos gestionada**: `ddev import-db` para cargar el esquema.
- **SSL local gratuito**: HTTPS en desarrollo sin configuracion.
- **Multi-proyecto**: puedes tener 10 proyectos simultaneos sin conflictos de puertos.

## Despliegue local con DDEV

### 1. Instalar DDEV

```powershell
# Windows (con Chocolatey)
choco install ddev

# O descarga desde https://ddev.com/get-started
```

### 2. Iniciar proyecto

```powershell
cd Webcaps

# Crear configuracion DDEV (solo la primera vez)
ddev config --project-type=php --docroot=public --create-docroot

# Levantar el entorno
ddev start
```

> `--docroot=public` asegura que solo la carpeta `public/` sea accesible desde la web.

### 3. Instalar dependencias PHP

```powershell
ddev composer install
```

### 4. Base de datos

```powershell
# Crear BD e importar esquema
ddev mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS webcaps CHARACTER SET utf8 COLLATE utf8_spanish_ci;"
ddev import-db --src=database/schema/webcaps.sql
```

Si no existe `database/schema/webcaps.sql`, usa el dump legacy:

```powershell
ddev import-db --src=webcaps.sql
```

### 5. Configurar entorno

```powershell
ddev exec "cp .env.example .env"
ddev exec "cp config/config.example.ini config/config.ini"
```

Edita `.env` y `config/config.ini` con tus credenciales (DDEV usa `db` como host, `db` como usuario, `db` como clave, `webcaps` como base de datos).

Ejemplo de `config/config.ini` para DDEV:

```ini
dns="mysql:host=db;dbname=webcaps"
usuario = "db"
clave = "db"
```

### 6. Acceder

- **Web**: `https://webcaps.ddev.site`
- **Panel admin**: `https://webcaps.ddev.site/acceso.php`
- **Mailpit** (emails capturados): `https://webcaps.ddev.site:8025`
- **phpMyAdmin**: `ddev launch -p`

### Comandos utiles de DDEV

```powershell
ddev start       # Levantar proyecto
ddev stop        # Detener proyecto
ddev restart     # Reiniciar
ddev ssh         # Entrar al contenedor
ddev logs        # Ver logs de errores
ddev mailpit     # Abrir Mailpit
ddev poweroff    # Apagar TODOS los proyectos DDEV
```

## Estructura del proyecto (reorganizada)

```text
Webcaps/
├── public/                  # Document root (unico accesible web)
│   ├── index.php            # Front Controller
│   ├── assets/              # CSS, JS, imagenes estaticas
│   ├── upload/              # Archivos subidos por usuarios
│   ├── Carro/               # Proxies del carrito (checkout)
│   ├── panel/               # Proxies del panel admin
│   └── .htaccess            # Rewrite rules
│
├── src/                     # Codigo PHP (autoload classmap)
│   ├── Models/              # Clases de acceso a datos
│   │   ├── Productos.php
│   │   ├── Pedidos.php
│   │   ├── Clientes.php
│   │   ├── Categorias.php
│   │   ├── Noticias.php
│   │   └── InfoPrenda.php
│   ├── Services/            # Logica de negocio reutilizable
│   │   └── EmailService.php
│   └── Helpers/             # Funciones auxiliares
│       └── Paths.php
│
├── templates/               # Plantillas HTML/PHP
│   ├── cabecera.php
│   ├── footer.php
│   ├── cabeceraAdmin.php
│   └── carroCabecera.php
│
├── Carro/                   # Logica legada del carrito
│   ├── index.php
│   ├── funcionesCarrito.php
│   ├── actualizar_carrito.php
│   └── eliminar_carrito.php
│
├── panel/                   # Panel admin (logica legada)
├── config/                  # Configuracion
│   ├── config.ini
│   └── config.example.ini
│
├── database/                # Esquemas SQL
├── docs/                    # Documentacion tecnica
├── composer.json
├── bootstrap.php            # Carga inicial de la aplicacion
└── vendor/                  # Dependencias Composer
```

## Convenciones recomendadas

- **Document root obligatorio**: el servidor debe apuntar siempre a `public/`.
- **No subir secretos**: `.env` y `config.ini` estan en `.gitignore`.
- **Mantener `vendor/` fuera de Git**.
- **Crear un branch por feature/fix**.

## Documentacion adicional

- `docs/PROJECT_STRUCTURE.md`
- `docs/DEVELOPMENT_PLAN.md`
- `docs/SECURITY_BASELINE.md`
- `docs/MIGRATION_STRUCTURE_PHASE2.md`
