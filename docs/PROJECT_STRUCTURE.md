# Estructura y organizacion

## Objetivo

Organizar el repositorio para que sea mas mantenible sin romper el flujo actual de produccion.

## Estructura vigente (estable)

- `assets/`: recursos de frontend.
- `Carro/`: funciones y flujo de carrito.
- `config/`: conexion y archivos de ejemplo.
- `panel/`: modulos de administracion.
- `src/`: clases de acceso a datos y servicios.
- `templates/`: cabeceras, pie de pagina y bloques reutilizables.
- `upload/`: imagenes subidas por administracion.

## Reglas de organizacion aplicadas

- Se centraliza documentacion tecnica en `docs/`.
- Se centralizan scripts operativos en `scripts/`.
- Se agregan ejemplos de configuracion (`.env.example`, `config.example.ini`).
- Se evita mover carpetas acopladas en rutas para no romper el sistema.
- Se habilita `public/index.php` como puente para migracion progresiva.
- Se crean carpetas destino `app/` y `database/`.

## Organizacion objetivo (fase futura)

```text
Webcaps/
|- app/
|  |- Controllers/
|  |- Services/
|  `- Repositories/
|- public/
|  |- index.php
|  |- assets/
|  `- upload/
|- config/
|- database/
|- templates/
|- docs/
`- tests/
```

Esta migracion se recomienda hacer por fases con pruebas de regresion en cada paso.

## Estado de migracion

- Fase 1: seguridad base y configuracion (completada).
- Fase 2: estructura puente (`app/`, `public/`, `database/`) (completada).
- Fase 3: traslado fisico de modulos (pendiente).
