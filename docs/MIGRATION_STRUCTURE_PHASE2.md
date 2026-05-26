# Migracion de estructura - Fase 2

## Objetivo

Mejorar la organizacion fisica del repositorio sin interrumpir el funcionamiento actual.

## Cambios aplicados en esta fase

- Se crea `public/index.php` como punto de entrada compatible.
- Se crea `app/` como carpeta destino para capas de aplicacion.
- Se crea `database/` para centralizar artefactos SQL y migraciones.
- Se mantiene estructura legacy operativa mientras se completa el traslado.

## Pasos siguientes recomendados

1. Migrar `src/` a `app/Services` y `app/Repositories` por modulo.
2. Mover paginas publicas (`index.php`, `conocenos.php`, `outfits.php`) a `public/`.
3. Consolidar `webcaps.sql` en `database/schema/webcaps.sql`.
4. Ajustar include paths con una funcion de ruta base.
5. Agregar pruebas de regresion por cada lote de archivos migrados.

## Regla de seguridad en migracion

No mover carpetas acopladas por rutas relativas en un solo cambio masivo.
Migrar por lotes pequenos y validar cada flujo funcional.

## Estado actual del dump SQL

- `database/schema/webcaps.sql`: ubicacion canonica nueva.
- `webcaps.sql`: copia legacy temporal para no romper referencias existentes.
