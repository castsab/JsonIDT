<?php

include_once 'Consultas.php';

class Json extends Consultas {
    
    public $_dominioServer = "https://raw.githubusercontent.com/castsab/JsonIDT/master";
   
    public function setCrearDirectorio($directorio){
        if (!file_exists($directorio)) 
        {
           mkdir($directorio, 0700);
        }
    }
    
    public function setCrearArchivoJson($array_datos,$ruta_archivo){
        
        $file = fopen($ruta_archivo, "w") or die("Problemas para generar el archivo Json - ( ".$ruta_archivo." )");
        
        if($this->_validarServer == 1)
        {
            fwrite($file, json_encode($array_datos,JSON_PRETTY_PRINT));
        }
        else
        {
            fwrite($file, json_encode($array_datos));
        }
        
    }

    public function setJsonIdioma($codigo_idioma){
        
        $a_idiomas = array();
        //$rutaArchivo = "Json/idioma.json";
        $rutaArchivo = ($codigo_idioma == 1)?"Json/idioma.json":"Json_".$codigo_idioma."/idioma.json";

        $rs = $this->getIdiomas();

        $i = 0;

        while ($rw = mysqli_fetch_array($rs)) {

            $a_idiomas[$i]['CODIGO_IDIOMA'] = $rw['CODIGO'];
            $a_idiomas[$i]['NOMBRE'] = utf8_encode($rw['NOMBRE']);

            $directorio = "Json_".$rw['CODIGO'];

            if($i <> 0 && $codigo_idioma == 1)
            {
                $this->setCrearDirectorio($directorio);
            }
            
            $i++;
        }

        /*echo '<pre>';
        print_r($a_idiomas);
        echo '</pre>';*/
        
        $this->setCrearArchivoJson($a_idiomas,$rutaArchivo);
        
    }

    public function setJsonClasificacion($codigo_idioma){
        
        $a_clasificaciones = array();
        
        //$rutaArchivo = "Json/clasificacion.json";
        $rutaArchivo = ($codigo_idioma == 1)?"Json/clasificacion.json":"Json_".$codigo_idioma."/clasificacion.json";
        
        $rs = $this->getClasificaciones();

        $search_array = array("6"=>"#E2423D",
                              "2"=>"#2A6093",
                              "3"=>"#668439",
                              "4"=>"#E03D76",
                              "5"=>"#D48528",
                              "1"=>"#0086B2",
                              "7"=>"#7F3F97");
        
        $i = 0;

        while ($rw = mysqli_fetch_array($rs)) {

            if($codigo_idioma <> 1){
                    
                $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$rw['CODIGO'],'TABLA'=>'CLASIFICACION', 'COD_IDIOMA'=>$codigo_idioma));
                $rw['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];

            }
            
            if(!empty($rw['NOMBRE']))
            {
            
                $a_clasificaciones[$i]['CODIGO'] = $rw['CODIGO'];
                $a_clasificaciones[$i]['NOMBRE'] = utf8_encode($rw['NOMBRE']);

                if (array_key_exists($rw['CODIGO'], $search_array)) {
                    $backgroundColor = $search_array[$rw['CODIGO']];
                }

                $a_clasificaciones[$i]['backgroundColor'] = $backgroundColor;
           
            }
            
            $i++;
        }

        /*echo '<pre>';
        print_r($a_clasificaciones);
        echo '</pre>';
        die();*/
        
        $this->setCrearArchivoJson($a_clasificaciones,$rutaArchivo);

    }
    
    public function setJsonTipologia($codigo_idioma){
        
        $rs = '';

        $a_tipologias = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/tipologia.json":"Json_".$codigo_idioma."/tipologia.json";
        
        $rs = $this->getClasificaciones();

        $i = 0;

        $a_tipologias['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';

            $rss = $this->getTipologias(array('COD_CLASIFICACION'=>$rw['CODIGO']));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {
                
                $NOMBRE = '';
                
                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$row['CODIGO'],'TABLA'=>'TIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                
                }
                
                if(!empty($row['NOMBRE']))
                {
                
                    $rgb = $this->getObtenerRgbImagen($row['ICONO']);
                    $COLOR_FILA = $this->getRgbConvertirAHexadecimal($rgb);

                    $rgb_fondo = $this->getObtenerRgbImagenFondo($row['ICONO']);
                    $COLOR_TRIANGULO = $this->getRgbConvertirAHexadecimal($rgb_fondo);

                    $nombreImagenTriangulo = $this->getCrearImagenTriangulo($row['CODIGO'],$rgb_fondo,'iconoTrianguloTipologia');

                    $a_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO'];
                    $a_tipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $row['COD_CLASIFICACION'];

                    $a_tipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);

                    $a_tipologias[$rw['CODIGO']][$j]['ICONO'] = utf8_encode($this->_dominioServer."".$row['ICONO']);
                    $a_tipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$row['IMAGEN']);

                    $a_tipologias[$rw['CODIGO']][$j]['TIENE_SUBTIPOLOGIA'] = $row['TIENE_SUBTIPOLOGIA'];

                    $a_tipologias[$rw['CODIGO']][$j]['backgroundColor'] = $COLOR_FILA;

                    $a_tipologias[$rw['CODIGO']][$j]['RUTA_TRIANGULO'] = utf8_encode($this->_dominioServer."/imagenes/".$nombreImagenTriangulo);
                
                }

                $j++;
            }
            //------------------------------------------------

            $i++;
        }

        /*echo '<pre>';
        print_r($a_tipologias);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_tipologias,$rutaArchivo);
        
    }
    
    public function setJsonSubtipologia($codigo_idioma){
        
        $rw = '';
        $rs = '';

        $a_subtipologias = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/subtipologia.json":"Json_".$codigo_idioma."/subtipologia.json";
        
        //$rutaArchivo = "Json/subtipologia.json";

        $rs = $this->getTipologias();

        $i = 0;

        $a_subtipologias['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';

            $rss = $this->getSubTipologias(array('COD_TIPOLOGIA'=>$rw['CODIGO']));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {

                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$row['CODIGO'],'TABLA'=>'SUBTIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                    $row['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];
                
                }
                
                if(!empty($row['NOMBRE']))
                {
                    /********************************************/
                    $a_subtipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $rw['COD_CLASIFICACION'];
                    $a_subtipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO'];

                    $a_subtipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);
                    $a_subtipologias[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);
                    $a_subtipologias[$rw['CODIGO']][$j]['CONTENIDO'] = utf8_encode($row['CONTENIDO']);

                    $a_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$row['IMAGEN']);
                    $a_subtipologias[$rw['CODIGO']][$j]['COD_TIPOLOGIA'] = $row['COD_TIPOLOGIA'];

                    /************************************/
                    $a_datos = $this->getPrestadorSubtipologia(array('CODIGO_SUBTIPOLOGIA'=>$row['CODIGO']));
                    $validar = mysqli_fetch_array($a_datos);

                    if(empty($validar))
                    {
                        $a_subtipologias[$rw['CODIGO']][$j]['TIENE_PRESTADOR'] = '0';
                    }
                    else
                    {
                        $a_subtipologias[$rw['CODIGO']][$j]['TIENE_PRESTADOR'] = '1';
                    }
                    /************************************/

                    /************************************/
                    $a_datoss = $this->getRutaPrestador(array('CODIGO'=>$row['CODIGO']));
                    $validar_ruta = mysqli_fetch_array($a_datoss);

                    if(empty($validar_ruta))
                    {
                        $a_subtipologias[$rw['CODIGO']][$j]['TIENE_RUTA'] = '0';
                    }
                    else
                    {
                        $a_subtipologias[$rw['CODIGO']][$j]['TIENE_RUTA'] = '1';
                    }
                    /************************************/

                    $a_subtipologias[$rw['CODIGO']][$j]['CONTENIDO'] = $row['CONTENIDO'];
                    /********************************************/
                
                }
                
                $j++;
            }
            //------------------------------------------------

            $i++;
        }

        /*echo '<pre>';
        print_r($a_subtipologias);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_subtipologias,$rutaArchivo);
        
    }
    
    public function setJsonZona($codigo_idioma){
        
        $rs = '';
        $rw = '';

        $a_zonas = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/zona.json":"Json_".$codigo_idioma."/zona.json";
        //$rutaArchivo = "Json/zona.json";

        $rs = $this->getZonas();

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

        $a_zonas['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            $backgroundColor = '';

            if($codigo_idioma <> 1){
                    
                $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$rw['CODIGO'],'TABLA'=>'ZONA', 'COD_IDIOMA'=>$codigo_idioma));
                $rw['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                $rw['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];

            }
            
            if(!empty($rw['NOMBRE']))
            {    
            
                /*******************************************/
                $a_zonas[$i]['CODIGO'] = $rw['CODIGO'];

                $a_zonas[$i]['NOMBRE'] = utf8_encode($rw['NOMBRE']);
                $a_zonas[$i]['DESCRIPCION'] = utf8_encode($rw['DESCRIPCION']);

                $a_zonas[$i]['IMAGEN'] = utf8_encode($this->_dominioServer."".$rw['IMAGEN']);

                $a_zonas[$i]['PUNTO_MINIMO'] = $rw['PUNTO_MINIMO'];
                $a_zonas[$i]['PUNTO_MAXIMO'] = $rw['PUNTO_MAXIMO'];
                //$a_zonas[$i]['backgroundColor'] = $rw['backgroundColor'];

                if (array_key_exists($rw['CODIGO'], $search_array)) {

                    $backgroundColor = $search_array[$rw['CODIGO']];
                    $rgb_fondo = $this->convertirHexadecimalARgb($backgroundColor);
                    $nombreImagenTriangulo = $this->getCrearImagenTriangulo($rw['CODIGO'],$rgb_fondo,'iconoTrianguloZona');
                }

                $a_zonas[$i]['backgroundColor'] = $backgroundColor;
                $a_zonas[$i]['RUTA_TRIANGULO'] = utf8_encode($this->_dominioServer."/imagenes/".$nombreImagenTriangulo);
                /*******************************************/
            
            }
            
            $i++;
        }
        
        /*echo '<pre>';
        print_r($a_zonas);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_zonas,$rutaArchivo);
        
    }
    
    public function setJsonZonaTipologia($codigo_idioma){
        
        $rs = '';
        $rw = '';

        $a_zonas_tipologias = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/zona_tipologia.json":"Json_".$codigo_idioma."/zona_tipologia.json";
        //$rutaArchivo = "Json/zona_tipologia.json";

        $rs = $this->getZonas();

        $i = 0;

        $a_zonas_tipologias['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';

            $rss = $this->getZonaTipologia(array('CODIGO_ZONA'=>$rw['CODIGO']));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {
                
                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$row['CODIGO_TIPOLOGIA'],'TABLA'=>'TIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                    $row['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];
                    
                }
                
                if(!empty($row['NOMBRE']))
                {
                    
                    $rgb = $this->getObtenerRgbImagen($row['ICONO']);
                    $COLOR_FILA = $this->getRgbConvertirAHexadecimal($rgb);

                    $rgb_fondo = $this->getObtenerRgbImagenFondo($row['ICONO']);
                    $COLOR_TRIANGULO = $this->getRgbConvertirAHexadecimal($rgb_fondo);

                    $nombreImagenTriangulo = $this->getCrearImagenTriangulo($row['CODIGO_TIPOLOGIA'],$rgb_fondo,'iconoTrianguloZonaTipologia');

                    //$a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $rw['CODIGO'];
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO'] = $row['CODIGO_TIPOLOGIA'];
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO_ZONA'] = $row['CODIGO_ZONA'];
                    //$a_zonas_tipologias[$rw['CODIGO']][$j]['CODIGO_TIPOLOGIA'] = utf8_encode($row['CODIGO_TIPOLOGIA']);

                    $a_zonas_tipologias[$rw['CODIGO']][$j]['NOMBRE'] = utf8_encode($row['NOMBRE']);
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);

                    $a_zonas_tipologias[$rw['CODIGO']][$j]['ICONO'] = utf8_encode($this->_dominioServer."".$row['ICONO']);
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$row['IMAGEN']);
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['COD_CLASIFICACION'] = $row['COD_CLASIFICACION'];
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['TIENE_SUBTIPOLOGIA'] = $row['TIENE_SUBTIPOLOGIA'];

                    $a_zonas_tipologias[$rw['CODIGO']][$j]['backgroundColor'] = $COLOR_FILA;
                    $a_zonas_tipologias[$rw['CODIGO']][$j]['RUTA_TRIANGULO'] = utf8_encode($this->_dominioServer."/imagenes/".$nombreImagenTriangulo);
                
                }

                $j++;
            }
            //------------------------------------------------

            $i++;
        }

        /*echo '<pre>';
        print_r($a_zonas_tipologias);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_zonas_tipologias,$rutaArchivo);
        
    }
    
    public function setJsonPrestador($codigo_idioma){
        
        $rs = '';
        $rw = '';

        $a_prestadores_subtipologias = array();
        
        $rs = $this->getSubTipologias();

        $i = 0;

        $a_prestadores_subtipologias['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';
            $a_prestadores_subtipologias = '';

            $rss = $this->getPrestadorSubtipologia(array('CODIGO_SUBTIPOLOGIA'=>$rw['CODIGO']));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {

                //$rutaArchivo = "Json/prestador_subtipologia_".$rw['CODIGO'].".json";
                
                $rutaArchivo = ($codigo_idioma == 1)?"Json/prestador_subtipologia_".$rw['CODIGO'].".json":"Json_".$codigo_idioma."/prestador_subtipologia_".$rw['CODIGO'].".json";
                
                
                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$rw['CODIGO'],'TABLA'=>'SUBTIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                    $row['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];

                }
                
                if(!empty($row['NOMBRE']))
                {
                    
                    //consulto la imagen del prestador
                    $img_prestador = $this->getImagenPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));
                    $IMAGEN = $this->getFotosPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));    

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

                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$img_prestador);
                    //$a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/museo_01.png";

                    //iconos
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_DIRECCION'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_dir.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TELEFONO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_tel.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_HORARIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_hor.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_MAIL'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_mail.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_PRECIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_precio.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TRANS'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_trans.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_WEB'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_web.png";
                    
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL_VIDEO'] = utf8_encode($row['URL_VIDEO']);
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL_AUDIO'] = utf8_encode($this->_dominioServer."".$row['URL_AUDIO']);

                    //print_r($IMAGEN);

                    if(!empty($IMAGEN))
                    {
                        for ($i = 0; $i < sizeof($IMAGEN); $i++) {
                            $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][$i] = utf8_encode($this->_dominioServer."".$IMAGEN[$i]);
                        }
                    }
                    else
                    {
                        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][0] = "";
                    }
                    
                }

                $j++;

            }
            //------------------------------------------------

            if($j <> 0){
               $this->setCrearArchivoJson($a_prestadores_subtipologias,$rutaArchivo);
            }

            $i++;
        }

        /*echo '<pre>';
        print_r($a_prestadores_subtipologias);
        echo '</pre>';*/
        
        //$this->setCrearArchivoJson($a_prestadores_subtipologias,"Json/prestador_subtipologia.json");
        
    }
    
    public function setJsonRutaPrestador($codigo_idioma){
        
        $rs = '';
        $rw = '';

        $a_ruta_prestador = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/ruta_prestador.json":"Json_".$codigo_idioma."/ruta_prestador.json";
        //$rutaArchivo = "Json/zona_tipologia.json";

        $rs = $this->getSubTipologias();

        $i = 0;

        $a_ruta_prestador['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';

            $rss = $this->getRutaPrestador(array('CODIGO'=>$rw['CODIGO']));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {

                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$row['CODIGO'],'TABLA'=>'PRESTADOR', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                    
                }
                
                if(!empty($row['NOMBRE']))
                {
                    
                    //consulto las fotos del prestador
                    $IMAGEN = $this->getFotosPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO']));  
                    
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
                    
                    //consulto la imagen del prestador
                    $img_prestador = $this->getImagenPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO']));
                    
                    $a_ruta_prestador[$rw['CODIGO']][$j]['CODIGO_PRESTADOR'] = $row['CODIGO'];
                    $a_ruta_prestador[$rw['CODIGO']][$j]['NOMBRE'] = $row['NOMBRE'];
                    $a_ruta_prestador[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$img_prestador);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['UBICACION'] = utf8_encode($row['UBICACION']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['DESCRIPCION'] = utf8_encode($row['DESCRIPCION']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['DIRECCION'] = utf8_encode($row['DIRECCION']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['TELEFONO'] = utf8_encode($TELEFONO);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['CORREO'] = $row['CORREO'];
                    $a_ruta_prestador[$rw['CODIGO']][$j]['URL'] = utf8_encode($row['URL']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['PRECIO_PROMEDIO'] = utf8_encode($row['PRECIO_PROMEDIO']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['HORARIO'] = utf8_encode($row['HORARIO']);

                    //iconos
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_DIRECCION'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_dir.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_TELEFONO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_tel.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_HORARIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_hor.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_MAIL'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_mail.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_PRECIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_precio.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_TRANS'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_trans.png";
                    $a_ruta_prestador[$rw['CODIGO']][$j]['ICONO_WEB'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_web.png";
                    
                    $a_ruta_prestador[$rw['CODIGO']][$j]['URL_VIDEO'] = utf8_encode($row['URL_VIDEO']);
                    $a_ruta_prestador[$rw['CODIGO']][$j]['URL_AUDIO'] = utf8_encode($this->_dominioServer."".$row['URL_AUDIO']);
                    
                    if(!empty($IMAGEN))
                    {
                        for ($i = 0; $i < sizeof($IMAGEN); $i++) {
                            $a_ruta_prestador[$rw['CODIGO']][$j]['FOTOS'][$i] = utf8_encode($this->_dominioServer."".$IMAGEN[$i]);
                        }
                    }
                    else
                    {
                        $a_ruta_prestador[$rw['CODIGO']][$j]['FOTOS'][0] = "";
                    }
                
                }
                
                $j++;
            }
            //------------------------------------------------

            $i++;
        }

        /*echo '<pre>';
        print_r($a_ruta_prestador);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_ruta_prestador,$rutaArchivo);
    }
    
    public function setJsonCarrusel($codigo_idioma) {
     
        $rw = '';
        $rs = '';

        $a_carrusel = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/carrusel.json":"Json_".$codigo_idioma."/carrusel.json";
        
        $rs = $this->getSubTipologias(array('COD_TIPOLOGIA'=>'1'));

        $j = 0;

        while ($row = mysqli_fetch_array($rs)) {

            if($codigo_idioma <> 1){

                $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$row['CODIGO'],'TABLA'=>'SUBTIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                $row['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];

            }
            
            if(!empty($row['NOMBRE']))
            {
                $a_carrusel[$j]['ImagenUrl'] = utf8_encode($this->_dominioServer."".$row['IMAGEN']);
                $a_carrusel[$j]['Titulo'] = utf8_encode($row['NOMBRE']);
                $a_carrusel[$j]['Texto'] = utf8_encode($row['DESCRIPCION']);
            }
            
            $j++;
        }
        //------------------------------------------------

        /*echo '<pre>';
        print_r($a_carrusel);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_carrusel,$rutaArchivo);
        
    }
    
    public function setTelefonoEmergencia($codigo_idioma) {
        
        $rw = '';
        $rs = '';

        $a_telefono_emergencia = array();
        
        $rutaArchivo = ($codigo_idioma == 1)?"Json/telefono_emergencia.json":"Json_".$codigo_idioma."/telefono_emergencia.json";
        
        $rs = $this->getSubTipologias(array('COD_TIPOLOGIA'=>'26'));
        
        $row = mysqli_fetch_array($rs);
        
        $cadenasalida = preg_replace("/\r\n+|\r+|\n+|\t+/i", "|", trim($row['CONTENIDO'])); 
        
        $a_contenido = explode("||",$cadenasalida);
        
        //print_r($a_contenido);
            
        for($i=0; $i < count($a_contenido); $i++)
        {   
            $str_tel = explode(":",$a_contenido[$i]);
            
            $a_telefono_emergencia[$i]['ID'] = $i;
            $a_telefono_emergencia[$i]['NOMBRE_TELEFONO'] = utf8_encode($str_tel[0]);
            $a_telefono_emergencia[$i]['NUMERO_TELEFONO'] = utf8_encode($str_tel[1]);
            $a_telefono_emergencia[$i]['ICONO_TELEFONO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_tel.png";
        }
            
        //------------------------------------------------

        /*echo '<pre>';
        print_r($a_telefono_emergencia);
        echo '</pre>';*/

        $this->setCrearArchivoJson($a_telefono_emergencia,$rutaArchivo);
        
    }
    
    public function setJsonPrestadorBuscador($codigo_idioma,$consultar){
        
        $rs = '';
        $rw = '';

        $a_prestadores_subtipologias = array();
        
        $rs = $this->getSubTipologias();

        $i = 0;

        $a_prestadores_subtipologias['FECHA_CREACION'] = date('Y-m-d');

        while ($rw = mysqli_fetch_array($rs)) {

            //------------------------------------------------
            $rss = '';
            //$a_prestadores_subtipologias = '';

            $rss = $this->getPrestadorSubtipologia(array('CODIGO_SUBTIPOLOGIA'=>$rw['CODIGO'],'CONSULTAR'=>$consultar));

            $j = 0;

            while ($row = mysqli_fetch_array($rss)) {

                //$rutaArchivo = "Json/prestador_subtipologia_".$rw['CODIGO'].".json";
                
                $rutaArchivo = ($codigo_idioma == 1)?"Json/prestador_subtipologia_buscador.json":"Json_".$codigo_idioma."/prestador_subtipologia_buscador.json";
                
                
                if($codigo_idioma <> 1){
                    
                    $a_idioma = $this->getTraduccionIdioma(array('COD_TABLA'=>$rw['CODIGO'],'TABLA'=>'SUBTIPOLOGIA', 'COD_IDIOMA'=>$codigo_idioma));
                    $row['NOMBRE'] = ($a_idioma['NOMBRE'] == '')?'':$a_idioma['NOMBRE'];
                    $row['DESCRIPCION'] = ($a_idioma['DESCRIPCION'] == '')?'':$a_idioma['DESCRIPCION'];

                }
                
                if(!empty($row['NOMBRE']))
                {
                    
                    //consulto la imagen del prestador
                    $img_prestador = $this->getImagenPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));
                    $IMAGEN = $this->getFotosPrestador(array('CODIGO_PRESTADOR'=>$row['CODIGO_PRESTADOR']));    

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

                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = utf8_encode($this->_dominioServer."".$img_prestador);
                    //$a_prestadores_subtipologias[$rw['CODIGO']][$j]['IMAGEN'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/museo_01.png";

                    //iconos
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_DIRECCION'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_dir.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TELEFONO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_tel.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_HORARIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_hor.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_MAIL'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_mail.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_PRECIO'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_precio.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_TRANS'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_trans.png";
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['ICONO_WEB'] = "https://raw.githubusercontent.com/castsab/JsonIDT/master/imagenes/ico_web.png";
                    
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL_VIDEO'] = utf8_encode($row['URL_VIDEO']);
                    $a_prestadores_subtipologias[$rw['CODIGO']][$j]['URL_AUDIO'] = utf8_encode($this->_dominioServer."".$row['URL_AUDIO']);

                    //print_r($IMAGEN);

                    if(!empty($IMAGEN))
                    {
                        for ($i = 0; $i < sizeof($IMAGEN); $i++) {
                            $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][$i] = utf8_encode($this->_dominioServer."".$IMAGEN[$i]);
                        }
                    }
                    else
                    {
                        $a_prestadores_subtipologias[$rw['CODIGO']][$j]['FOTOS'][0] = "";
                    }
                    
                }

                $j++;

            }
            //------------------------------------------------

            /*if($j <> 0){
               $this->setCrearArchivoJson($a_prestadores_subtipologias,$rutaArchivo);
            }*/

            $i++;
        }

        echo '<pre>';
        print_r($a_prestadores_subtipologias);
        echo '</pre>';
        
        //$this->setCrearArchivoJson($a_prestadores_subtipologias,"Json/prestador_subtipologia.json");
        
        $json = '';
        
        if($this->_validarServer == 1)
        {
            $json = json_encode($a_prestadores_subtipologias,JSON_PRETTY_PRINT);
        }
        else
        {
            $json = json_encode($a_prestadores_subtipologias);
        } 
        
        return $json;
        
    }
    
}

?>