<?php

header ("Content-Type:text/json");

include_once 'Json.php';
$C_Json = new Json();

$rs = $C_Json->getIdiomas();

$codigo_idioma = '1';

$consultar = $_POST['CONSULTAR'];

//=========================
//json prestadores
$json = $C_Json->setJsonPrestadorBuscador($codigo_idioma,$consultar);
//=========================

echo $json;

?>