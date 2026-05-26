<?php
/**
 * Front Controller - Punto de entrada único web.
 * Cambia al directorio raíz del proyecto para mantener compatibilidad
 * con todas las rutas relativas del código legado.
 */
chdir(dirname(__DIR__));
require_once 'index.php';
