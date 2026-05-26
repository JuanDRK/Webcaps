# Plan de desarrollo tecnico

## Semana 1 (seguridad y base)

- [x] Endurecer login de administracion.
- [x] Mover credenciales SMTP a entorno.
- [x] Endurecer subida de imagenes.
- [x] Crear base documental (`README` + `docs`).

## Semana 2 (calidad y deuda tecnica)

- [ ] Crear `.env` real por entorno (local, stage, prod).
- [ ] Unificar acceso DB en `PDO` (evitar mezclas).
- [ ] Extraer validaciones repetidas a funciones reutilizables.
- [ ] Crear pagina central de manejo de errores.

## Semana 3 (testing y entrega continua)

- [ ] Configurar pruebas minimas de negocio.
- [ ] Definir pipeline de CI (lint + tests).
- [ ] Crear plantilla de PR y checklist de QA.

## Prioridad funcional inmediata

1. Login admin
2. Flujo checkout
3. Registro y actualizacion de productos
4. Gestion de pedidos
