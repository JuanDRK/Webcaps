<?php

namespace capsweb;
use PDO;

require_once __DIR__ . '/../Helpers/Paths.php';

class clientes
{
    private $config;
    private $cn = null;

    public function __construct()
    {

        $this->config = parse_ini_file(config_path('config.ini'));

        $this->cn = new PDO($this->config['dns'], $this->config['usuario'], $this->config['clave'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ));
    }

    public function registrar($_params)
    {
        $sql = "INSERT INTO `clientes`(`nombre`, `apellidos`, `email`, `telefono`, `direccion`) 
        VALUES (:nombre,:apellidos,:email,:telefono,:direccion)";

        $resultado = $this->cn->prepare($sql);

        $_array = array(
            ":nombre" => $_params['nombre'],
            ":apellidos" => $_params['apellidos'],
            ":email" => $_params['email'],
            ":telefono" => $_params['telefono'],
            ":direccion" => $_params['direccion']
        );

        if ($resultado->execute($_array))
            return $this->cn->lastInsertId();

        return false;
    }

    public function mostrarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM `clientes` WHERE `id` = :id LIMIT 1";
        $resultado = $this->cn->prepare($sql);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data = $resultado->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
