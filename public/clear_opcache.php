<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache limpiado correctamente. Recarga la pagina principal.';
} else {
    echo 'OPcache no esta disponible.';
}
