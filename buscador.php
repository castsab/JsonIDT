<?php

include_once 'Json.php';
$C_Json = new Json();

$rs = $C_Json->getIdiomas();

$consultar = 'CASA';
$codigo_idioma = '1';

//$consultar = $_POST['CONSULTAR'];
//$codigo_idioma = $_POST['IDIOMA'];

//------------------------------------------
//------------------------------------------
//json prestadores
$C_Json->setJsonPrestadorBuscador($codigo_idioma,$consultar);
//------------------------------------------
//------------------------------------------
    
?>