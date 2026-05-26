# Seguridad base del proyecto

## Implementado

- Login de admin con consulta preparada.
- Rotacion de session id en autenticacion.
- Verificacion de contrasena compatible con hash.
- Carga de imagen con validacion de MIME, tamano y nombre seguro.
- Configuracion SMTP fuera de codigo hardcodeado.

## Recomendaciones siguientes

- Migrar contrasenas admin a `password_hash`.
- Limitar intentos de login por IP/usuario.
- Definir politica CSP y cabeceras de seguridad globales.
- Separar secretos por entorno.
- Evitar exponer errores sensibles al cliente final.

## Checklist para cada release

- [ ] Revisar que no haya secretos en commits.
- [ ] Validar que uploads cumplan politica de archivos.
- [ ] Probar autenticacion y control de acceso.
- [ ] Verificar dependencias con `composer audit`.
