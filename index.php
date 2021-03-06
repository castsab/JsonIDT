<?php

include_once 'Json.php';
$C_Json = new Json();

$rs = $C_Json->getIdiomas();

while ($rw = mysqli_fetch_array($rs)) {

    //------------------------------------------
    //------------------------------------------
    //json de idiomas
    $C_Json->setJsonIdioma($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
    //------------------------------------------
    //------------------------------------------
    //json del carrusel
    $C_Json->setJsonCarrusel($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
    //------------------------------------------
    //------------------------------------------
    //json clasificaciones
    $C_Json->setJsonClasificacion($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------

    //------------------------------------------
    //json tipologias
    $C_Json->setJsonTipologia($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------

    //------------------------------------------
    //------------------------------------------
    //json subtipologias
    $C_Json->setJsonSubtipologia($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------

    //------------------------------------------
    //------------------------------------------
    //json de las zonas
    $C_Json->setJsonZona($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------

    //------------------------------------------
    //------------------------------------------
    //json zonas por tipologias
    $C_Json->setJsonZonaTipologia($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------

    //------------------------------------------
    //------------------------------------------
    //json prestadores
    $C_Json->setJsonPrestador($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
    //------------------------------------------
    //------------------------------------------
    //json de rutas prestadores
    $C_Json->setJsonRutaPrestador($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
    //------------------------------------------
    //------------------------------------------
    //json telefonos de emergencia
    $C_Json->setTelefonoEmergencia($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
    //------------------------------------------
    //------------------------------------------
    //json json etiqueta
    $C_Json->setJsonEtiqueta($rw['CODIGO']);
    //------------------------------------------
    //------------------------------------------
    
}

?>
