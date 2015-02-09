<?php

class Conexion {

    private $servidor = 'localhost';
    private $usuario = 'root';
    private $password = '';
    private $base_datos = 'idt_pro';
    private $link;
    private $stmt;
    static $_instance;

    public function __construct() {
        
        $this->_validarServer = $this->getServidor();
        
        if($this->_validarServer != 1)
        {
            $this->usuario = 'consultoridt';
            $this->password = 'pr3pr0ducc10nc0nsult0r1dt';
        }
        
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
    
    public function getServidor() {
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
        {
            return true;
        } 
        else 
        {
            return false;
        }
        
    }

}

?>
