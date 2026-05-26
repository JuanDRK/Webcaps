<?php

if (!function_exists('project_path')) {
    function project_path($path = '')
    {
        $basePath = dirname(__DIR__, 2);
        if ($path === '') {
            return $basePath;
        }

        return $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return project_path('config/' . ltrim($path, '/'));
    }
}

if (!function_exists('storage_upload_path')) {
    function storage_upload_path($path = '')
    {
        return project_path('public/upload/' . ltrim($path, '/'));
    }
}
