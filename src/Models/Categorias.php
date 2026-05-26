<?php

namespace capsweb;

require_once __DIR__ . '/../Helpers/Paths.php';

class Categorias
{

    private $config;
    private $cn = null;

    public function __construct()
    {

        $this->config = parse_ini_file(config_path('config.ini'));

        $this->cn = new \PDO($this->config['dns'], $this->config['usuario'], $this->config['clave'], array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ));
    }

    public function mostrar(): array|false
    {
        $sql = "SELECT * FROM categorias";

        $resultado = $this->cn->prepare($sql);

        if ($resultado->execute())
            return $resultado->fetchAll();

        return false;
    }

    public function mostrarporCategoria(string $seleccion): array|false
    {
        $sql = "SELECT * FROM categorias where tipoDePrenda = :seleccion ";

        $resultado = $this->cn->prepare($sql);

        if ($resultado->execute())
            return $resultado->fetchAll();

        return false;
    }

    public function registrar($_params)
    {
        $sql = "INSERT INTO  `categorias` (`categoria`) VALUES (:categoria)";

        $resultado = $this->cn->prepare($sql);
        $_array = array(
            'categoria' => $_params['categoria']
        );

        if ($resultado->execute($_array))
            return true;
        return false;
    }
}
