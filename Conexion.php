<?php

class Conexion {

    private $servidor = 'localhost';
    private $usuario = 'root';
    private $password = '';
    private $base_datos = 'idt';
    private $link;
    private $stmt;
    static $_instance;

    public function __construct() {
        $this->conectar();
    }

    public function conectar() {
        $this->link = mysqli_connect($this->servidor, $this->usuario, $this->password,$this->base_datos);
        //mysqli_select_db($this->base_datos, $this->link);
    }

    public function ejecutar($sql) {
        $this->stmt = mysqli_query($this->link,$sql);
        return $this->stmt;
    }

}

?>
