<?php

include_once 'Consultas.php';
$bd = new Consultas();

$urlFolderImagenes = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes";
//------------------------------------------
//------------------------------------------
//Paso 1, json menu principal

$a_clasificaciones = array();

$rs = $bd->getClasificaciones();

$i = 0;

while ($rw = mysqli_fetch_array($rs)) {

    $a_clasificaciones[$i]['CODIGO'] = $rw['CODIGO'];
    $a_clasificaciones[$i]['NOMBRE'] = utf8_encode($rw['NOMBRE']);

    $i++;
}

/*echo '<pre>';
print_r($a_clasificaciones);
echo '</pre>';*/

/*$file = fopen("Json/clasificacion.json", "w") or die("Problemas para generar el documento (clasificacion_json)");
fwrite($file, json_encode($a_clasificaciones,JSON_PRETTY_PRINT));*/

//------------------------------------------

//------------------------------------------
//Paso 2, json primer nivel del menu

$rs = '';

$a_tipologias = array();

$rs = $bd->getClasificaciones();

$i = 0;

while ($rw = mysqli_fetch_array($rs)) {

    //------------------------------------------------
    $rss = '';
    
    $rss = $bd->getTipologias(array('COD_CLASIFICACION'=>$rw['CODIGO']));
    
    $j = 0;
    
    while ($row = mysqli_fetch_array($rss)) {
        
        if($row['CODIGO'] == 41)
        {
            $rgb = $bd->getObtenerRgbImagen("imagenes/iconoTipologia1.png");
            $COLOR_FILA = $bd->getRgbConvertirAHexadecimal($rgb);
        
            $rgb_fondo = $bd->getObtenerRgbImagenFondo("imagenes/iconoTipologia1.png");
            $COLOR_TRIANGULO = $bd->getRgbConvertirAHexadecimal($rgb_fondo);
        }
        else
        {
            
            $rgb = $bd->getObtenerRgbImagen("imagenes/iconoTipologia9.png");
            $COLOR_FILA = $bd->getRgbConvertirAHexadecimal($rgb);

            $rgb_fondo = $bd->getObtenerRgbImagenFondo("imagenes/iconoTipologia9.png");
            $COLOR_TRIANGULO = $bd->getRgbConvertirAHexadecimal($rgb_fondo);
        
        }
        
        $nombreImagenTriangulo = $bd->getCrearImagenTriangulo($row['CODIGO'],$rgb_fondo,'iconoTrianguloTipologia');
        
        $a_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO'];
        $a_tipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $row['COD_CLASIFICACION'];
        $a_tipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);
        $a_tipologias[$rw['CODIGO']][$j]['ICONO'] = $row['ICONO'];
        
        //$a_tipologias[$rw['CODIGO']][$j]['ICONO'] = utf8_encode("https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/iconoTipologia9.png");
        $a_tipologias[$rw['CODIGO']][$j]['IMAGEN'] = $row['IMAGEN'];
        
        $a_tipologias[$rw['CODIGO']][$j]['TIENE_SUBTIPOLOGIA'] = $row['TIENE_SUBTIPOLOGIA'];
        $a_tipologias[$rw['CODIGO']][$j]['backgroundColor'] = $COLOR_FILA;
        $a_tipologias[$rw['CODIGO']][$j]['RUTA_TRIANGULO'] = utf8_encode($urlFolderImagenes."/".$nombreImagenTriangulo);
        
        $j++;
    }
    //------------------------------------------------
    
    $i++;
}

/*echo '<pre>';
print_r($a_tipologias);
echo '</pre>';*/

$file = fopen("Json/tipologia.json", "w") or die("Problemas para generar el documento (tipologia_json)");

fwrite($file, json_encode($a_tipologias,JSON_PRETTY_PRINT));
//------------------------------------------
//------------------------------------------

//------------------------------------------
//------------------------------------------
//Paso 3, json segundo nivel del menu

$rs = '';
$rw = '';

$a_subtipologias = array();

$rs = $bd->getTipologias();

$i = 0;

while ($rw = mysqli_fetch_array($rs)) {

    //------------------------------------------------
    $rss = '';
    
    $rss = $bd->getSubTipologias(array('COD_TIPOLOGIA'=>$rw['CODIGO']));
    
    $j = 0;
    
    while ($row = mysqli_fetch_array($rss)) {
        
        $a_subtipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $rw['COD_CLASIFICACION'];
        $a_subtipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO'];
        $a_subtipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);
        $a_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = $row['IMAGEN'];
        $a_subtipologias[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);
        $a_subtipologias[$rw['CODIGO']][$j]['CONTENIDO'] = utf8_encode($row['CONTENIDO']);
        $a_subtipologias[$rw['CODIGO']][$j]['COD_TIPOLOGIA'] = $row['COD_TIPOLOGIA'];
        
        $j++;
    }
    //------------------------------------------------
    
    $i++;
}

/*echo '<pre>';
print_r($a_subtipologias);
echo '</pre>';*/

$file = fopen("Json/subtipologia.json", "w") or die("Problemas para generar el documento (subtipologia_json)");

fwrite($file, json_encode($a_subtipologias,JSON_PRETTY_PRINT));
//------------------------------------------
//------------------------------------------

//------------------------------------------
//------------------------------------------
//Paso 4, json de las zonas

$rs = '';
$rw = '';

$a_zonas = array();

$rs = $bd->getZonas();

$i = 0;

$search_array = array("1"=>"#5f87ad",
                      "2"=>"#185c92",
                      "3"=>"#0885c4",
                      "4"=>"#12b5a5",
                      "5"=>"#199188",
                      "6"=>"#1a8e64",
                      "7"=>"#1b8943",
                      "8"=>"#0b890b",
                      "9"=>"#698c0a",
                      "10"=>"#89620b",
                      "11"=>"#844b0d",
                      "12"=>"#82190f");

while ($rw = mysqli_fetch_array($rs)) {
    
    $backgroundColor = '';
    
    $a_zonas[$i]['CODIGO'] = $rw['CODIGO'];
    $a_zonas[$i]['NOMBRE'] = utf8_encode($rw['NOMBRE']);
    $a_zonas[$i]['DESCRIPCION'] = utf8_encode($rw['DESCRIPCION']);
    $a_zonas[$i]['IMAGEN'] = $rw['IMAGEN'];
    $a_zonas[$i]['PUNTO_MINIMO'] = $rw['PUNTO_MINIMO'];
    $a_zonas[$i]['PUNTO_MAXIMO'] = $rw['PUNTO_MAXIMO'];
    $a_zonas[$i]['backgroundColor'] = $rw['backgroundColor'];
    
    if (array_key_exists($rw['CODIGO'], $search_array)) {
        
        $backgroundColor = $search_array[$rw['CODIGO']];
        
        $rgb_fondo = $bd->convertirHexadecimalARgb($backgroundColor);
        $nombreImagenTriangulo = $bd->getCrearImagenTriangulo($rw['CODIGO'],$rgb_fondo,'iconoTrianguloZona');
        
    }
    
    $a_zonas[$i]['backgroundColor'] = $backgroundColor;
    $a_zonas[$i]['RUTA_TRIANGULO'] = utf8_encode($urlFolderImagenes."/".$nombreImagenTriangulo);
    
    $i++;
}

/*echo '<pre>';
print_r($a_zonas);
echo '</pre>';*/

$file = fopen("Json/zona.json", "w") or die("Problemas para generar el documento (zona_json)");

fwrite($file, json_encode($a_zonas,JSON_PRETTY_PRINT));
//------------------------------------------
//------------------------------------------



//------------------------------------------
//------------------------------------------
//Paso 5, json zonas por tipologias

$rs = '';
$rw = '';

$a_zonas_tipologias = array();

$rs = $bd->getZonas();

$i = 0;

while ($rw = mysqli_fetch_array($rs)) {

    //------------------------------------------------
    $rss = '';
    
    $rss = $bd->getZonaTipologia(array('CODIGO_ZONA'=>$rw['CODIGO']));
    
    $j = 0;
    
    while ($row = mysqli_fetch_array($rss)) {
        
        if($row['CODIGO_TIPOLOGIA'] == 41)
        {
            $rgb = $bd->getObtenerRgbImagen("imagenes/iconoTipologia1.png");
            $COLOR_FILA = $bd->getRgbConvertirAHexadecimal($rgb);
        
            $rgb_fondo = $bd->getObtenerRgbImagenFondo("imagenes/iconoTipologia1.png");
            $COLOR_TRIANGULO = $bd->getRgbConvertirAHexadecimal($rgb_fondo);
        }
        else
        {
            
            $rgb = $bd->getObtenerRgbImagen("imagenes/iconoTipologia9.png");
            $COLOR_FILA = $bd->getRgbConvertirAHexadecimal($rgb);

            $rgb_fondo = $bd->getObtenerRgbImagenFondo("imagenes/iconoTipologia9.png");
            $COLOR_TRIANGULO = $bd->getRgbConvertirAHexadecimal($rgb_fondo);
        
        }
        
        $nombreImagenTriangulo = $bd->getCrearImagenTriangulo($row['CODIGO_TIPOLOGIA'],$rgb_fondo,'iconoTrianguloZonaTipologia');
        
        //$a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $rw['CODIGO'];
        $a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO_TIPOLOGIA'];
        $a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO_ZONA'] = $row['CODIGO_ZONA'];
        //$a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO_TIPOLOGIA'] = utf8_encode($row['CODIGO_TIPOLOGIA']);
        $a_zonas_tipologias[$rw['CODIGO']][$j]['NOMBRE'] = $row['NOMBRE'];
        $a_zonas_tipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($row['IMAGEN']);
        $a_zonas_tipologias[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);
        $a_zonas_tipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $row['COD_CLASIFICACION'];
        $a_zonas_tipologias[$rw['CODIGO']][$j]['TIENE_SUBTIPOLOGIA'] = $row['TIENE_SUBTIPOLOGIA'];
        
        $a_zonas_tipologias[$rw['CODIGO']][$j]['ICONO'] = $row['ICONO'];
        $a_zonas_tipologias[$rw['CODIGO']][$j]['backgroundColor'] = $COLOR_FILA;
        $a_zonas_tipologias[$rw['CODIGO']][$j]['RUTA_TRIANGULO'] = utf8_encode($urlFolderImagenes."/".$nombreImagenTriangulo);
        
        $j++;
    }
    //------------------------------------------------
    
    $i++;
}

/*echo '<pre>';
print_r($a_zonas_tipologias);
echo '</pre>';*/

$file = fopen("Json/zona_tipologia.json", "w") or die("Problemas para generar el documento (zona_tipologia_json)");

fwrite($file, json_encode($a_zonas_tipologias,JSON_PRETTY_PRINT));
//------------------------------------------
//------------------------------------------


//------------------------------------------
//------------------------------------------
//Paso 6, json prestadores

$rs = '';
$rw = '';

$a_prestadores_subtipologias = array();

$rs = $bd->getSubTipologias();

$i = 0;

while ($rw = mysqli_fetch_array($rs)) {

    //------------------------------------------------
    $rss = '';
    //$a_prestadores_subtipologias = '';
    
    $rss = $bd->getPrestadorSubtipologia(array('CODIGO_SUBTIPOLOGIA'=>$rw['CODIGO']));
    
    $j = 0;
    
    while ($row = mysqli_fetch_array($rss)) {
        
        //consulto la imagen del prestador
        $img_prestador = $bd->getImagenPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));
        $IMAGEN = $bd->getFotosPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));    
        
        /*echo "<pre>"; 
        print_r($IMAGEN);
        echo "</pre>";*/
        
        if(!empty($img_prestador)){
            $img_prestador = utf8_encode($img_prestador);
        }
        
        $str_telefono = explode(":",$row['TELEFONO']);
        
        if(!empty($str_telefono[1]))
        {
            $a_telefono = explode(" ",$str_telefono[1]);
            $TELEFONO = $a_telefono[0];
        }
        else
        {
            $TELEFONO = $row['TELEFONO'];
        }
       
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO_PRESTADOR'];
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['DIRECCION'] = utf8_encode($row['DIRECCION']);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['TELEFONO'] = utf8_encode($TELEFONO);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['CORREO'] = $row['CORREO'];
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL'] = utf8_encode($row['URL']);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['UBICACION'] = $row['UBICACION'];
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['PRECIO_PROMEDIO'] = utf8_encode($row['PRECIO_PROMEDIO']);
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['HORARIO'] = utf8_encode($row['HORARIO']);
        
        //$a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = $img_prestador;
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/museo_01.png";
        
        //iconos
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_DIRECCION'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_dir.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TELEFONO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_tel.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_HORARIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_hor.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_MAIL'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_mail.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_PRECIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_precio.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TRANS'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_trans.png";
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_WEB'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_web.png";
        
        
        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL_VIDEO'] = utf8_encode($row['URL_VIDEO']);
        
        //print_r($IMAGEN);
        
        if(!empty($IMAGEN))
        {
            for ($i = 0; $i < sizeof($IMAGEN); $i++) {
                $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][$i] = utf8_encode($IMAGEN[$i]);
            }
        }
        else
        {
            $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][0] = "";
        }
        
        $j++;
        
    }
    //------------------------------------------------
    
    if($j != 0){
        $file = fopen("Json/prestadores/prestador_subtipologia_".$rw['CODIGO'].".json", "w") or die("Problemas para generar el documento (prestador_subtipologia_json)");
        fwrite($file, json_encode($a_prestadores_subtipologias,JSON_PRETTY_PRINT));
    }
    
    $i++;
}

/*echo '<pre>';
print_r($a_prestadores_subtipologias);
echo '</pre>';*/

$file = fopen("Json/prestador_subtipologia.json", "w") or die("Problemas para generar el documento (prestador_subtipologia_json)");
fwrite($file, json_encode($a_prestadores_subtipologias,JSON_PRETTY_PRINT));
//------------------------------------------
//------------------------------------------

?>
