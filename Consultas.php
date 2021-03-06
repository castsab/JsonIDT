<?php

include_once 'Conexion.php';

class Consultas extends Conexion {

    public $_conexion = '';
    
    public $_TB_CLASIFICACION = '';
    public $_TB_TIPOLOGIA = '';
    public $_TB_SUBTIPOLOGIA = '';
    public $_TB_ZONA = '';
    public $_TB_ZONA_TIPOLOGIA = '';
    public $_TB_IMAGENES = '';
    public $_TB_PRESTADOR_SUBTIPOLOGIA = '';
    public $_TB_PRESTADOR = '';
    public $_TB_IDIOMA = '';
    public $_TB_TRADUCCION = '';
    public $_TB_RUTA_PRESTADOR = '';
    public $_validarServer = '';
    public $_dominioServer = '';
    public $_TB_ETIQUETA = '';
    public $_delimitador = '';
    public $_codigoTelefono = '';
    
    public function __construct() {
        
        $this->_TB_CLASIFICACION = 'CLASIFICACION';
        $this->_TB_TIPOLOGIA = 'TIPOLOGIA';
        $this->_TB_SUBTIPOLOGIA = 'SUBTIPOLOGIA';
        $this->_TB_ZONA = 'ZONA';
        $this->_TB_ZONA_TIPOLOGIA = 'ZONA_TIPOLOGIA';
        $this->_TB_IMAGENES = 'IMAGENES';
        $this->_TB_PRESTADOR_SUBTIPOLOGIA = 'PRESTADOR_SUBTIPOLOGIA';
        $this->_TB_PRESTADOR = 'PRESTADOR';
        $this->_TB_IDIOMA = 'IDIOMA';
        $this->_TB_TRADUCCION = 'TRADUCCION';
        $this->_TB_RUTA_PRESTADOR = 'RUTA_PRESTADOR';
        $this->_TB_ETIQUETA = 'ETIQUETA';
        $this->_delimitador = 'imagenesBD';
        $this->_codigoTelefono = 26;
        
        $this->_validarServer = $this->getServidor();
        
        if($this->_validarServer == 1){
            $this->_dominioServer = "https://raw.githubusercontent.com/castsab/JsonIDT/master";
        }else{
            $this->_dominioServer = "http://pruebacidt.bogota.gov.co";
        }
        
        parent::__construct();
    }

    public function getClasificaciones() {

        $sql = 'select CODIGO As CODIGO,UPPER(NOMBRE) As NOMBRE from '.$this->_TB_CLASIFICACION.' where CODIGO IN (1,2,3,4,5,7) ORDER BY FIELD(CODIGO,1,2,3,4,5,7);';
        $rs = $this->ejecutar($sql);
        return $rs;
    }
    
    public function getTipologias($param = '') {
        
        $cond = '';
        
        if(!empty($param['COD_CLASIFICACION']))
        {
            $cond = 'where COD_CLASIFICACION= '.$param['COD_CLASIFICACION'].' ';
        }
        
        $sql = 'select 
                    CODIGO As CODIGO,
                    COD_CLASIFICACION As COD_CLASIFICACION,
                    NOMBRE As NOMBRE,
                    ICONO As ICONO,
                    IMAGEN As IMAGEN,
                    TIENE_SUBTIPOLOGIA As TIENE_SUBTIPOLOGIA
                from 
                    '.$this->_TB_TIPOLOGIA.'
                '.$cond.' ORDER BY COD_CLASIFICACION ASC';
        
        $rs = $this->ejecutar($sql);
        return $rs;
    }
    
    public function getSubTipologias($param = '') {

        $cond = '';
        
        if(!empty($param['COD_TIPOLOGIA']))
        {
            $cond = 'where COD_TIPOLOGIA= '.$param['COD_TIPOLOGIA'].' ';
        }
        
        $sql = 'select 
                    CODIGO As CODIGO,
                    NOMBRE As NOMBRE,
                    IMAGEN As IMAGEN,
                    DESCRIPCION As DESCRIPCION,
                    CONTENIDO As CONTENIDO,
                    COD_TIPOLOGIA As COD_TIPOLOGIA
                from 
                    '.$this->_TB_SUBTIPOLOGIA.'
                    '.$cond.' ORDER BY CODIGO ASC';
        
        //echo '<br>(getSubTipologias) - sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        return $rs;
    }
    
    public function getZonas() {

        $sql = 'select 
                    CODIGO As CODIGO,
                    NOMBRE As NOMBRE,
                    DESCRIPCION As DESCRIPCION,
                    IMAGEN As IMAGEN,
                    PUNTO_MINIMO As PUNTO_MINIMO,
                    PUNTO_MAXIMO As PUNTO_MAXIMO
                from 
                    '.$this->_TB_ZONA.'
                ORDER BY CODIGO ASC';
        
        $rs = $this->ejecutar($sql);
        return $rs;
    }
    
    public function getZonaTipologia($param = '') {
        
        $cond = '';
        
        if(!empty($param['CODIGO_ZONA']))
        {
            $cond = 'where zt.CODIGO_ZONA= '.$param['CODIGO_ZONA'].' ';
        }
        
        $sql = "select
                    zt.CODIGO_ZONA As CODIGO_ZONA, 
                    zt.CODIGO_TIPOLOGIA As CODIGO_TIPOLOGIA,
                    t.NOMBRE As NOMBRE,
                    t.IMAGEN As IMAGEN,
                    t.DESCRIPCION As DESCRIPCION,
                    t.COD_CLASIFICACION,
                    t.TIENE_SUBTIPOLOGIA,
                    t.ICONO As ICONO
                from 
                    $this->_TB_ZONA_TIPOLOGIA zt 
                    inner JOIN $this->_TB_TIPOLOGIA t on zt.CODIGO_TIPOLOGIA=t.CODIGO
                    $cond ORDER BY zt.CODIGO_ZONA,t.CODIGO";
        
        //echo '<br>(getZonaTipologia) - sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        return $rs;
        
    }
    
    public function getImagenPrestador($param = '') {
        
        $cond = '';
        $rs = '';
        $img_prestador = '';
        
        if(!empty($param['CODIGO_PRESTADOR']))
        {
            //$cond = 'where CODIGO_PRESTADOR= '.$param['CODIGO_PRESTADOR'].' ';
            $sql = 'select * from '.$this->_TB_IMAGENES.' where CODIGO_PRESTADOR='.$param['CODIGO_PRESTADOR'].' limit 1';
            
            //echo '<br>(getImagenPrestador)- sql { '.$sql.' }<br>';
        
            $rs = $this->ejecutar($sql);
            
            $data = mysqli_fetch_array($rs); 
            
            $img_prestador = $data[1];
            
        }

       
        return $img_prestador;
    }

    public function getFotosPrestador($param = '') {
        
        $cond = '';
        $rs = '';
        //$img_prestador = '';
        
        if(!empty($param['CODIGO_PRESTADOR']))
        {
            //$cond = 'where CODIGO_PRESTADOR= '.$param['CODIGO_PRESTADOR'].' ';
            $sql = 'select IMAGEN As IMAGEN from '.$this->_TB_IMAGENES.'
                    where CODIGO_PRESTADOR='.$param['CODIGO_PRESTADOR'].' limit 1';
            
            //echo '<br>(getImagenPrestador)- sql { '.$sql.' }<br>';
        
            $rs = $this->ejecutar($sql);
            
            $IMAGEN = '';
            
            while ($row = mysqli_fetch_array($rs)) {
                $IMAGEN[] = $row['IMAGEN'];
            }
            
        }

        return $IMAGEN;
    }
    
    public function getPrestadorSubtipologia($param = ''){
        
        $cond = '';
        
        if(!empty($param['CODIGO_SUBTIPOLOGIA']))
        {
            $cond = 'where s.CODIGO= '.$param['CODIGO_SUBTIPOLOGIA'].' ';
        }
        else
        {
            return false;
        }
        
        $sql = "select
                    s.CODIGO As CODIGO_SUBTIPOLOGIA,
                    s.COD_TIPOLOGIA AS COD_TIPOLOGIA,
                    s.NOMBRE AS NOMBRE_TIPOLOGIA,
                    ps.CODIGO_PRESTADOR,
                    p.COD_ZONA,
                    p.NOMBRE,
                    p.DESCRIPCION,
                    p.DIRECCION,
                    p.TELEFONO,
                    p.CORREO,
                    p.URL,
                    p.UBICACION,
                    p.PRECIO_PROMEDIO,
                    p.HORARIO,
                    p.URL_VIDEO,
                    p.URL_AUDIO
                from $this->_TB_SUBTIPOLOGIA s inner join $this->_TB_PRESTADOR_SUBTIPOLOGIA ps on s.CODIGO=ps.CODIGO_SUBTIPOLOGIA
                                               inner join $this->_TB_PRESTADOR p on p.CODIGO=ps.CODIGO_PRESTADOR 
                $cond";
        
        //echo '<br>(getPrestadorSubtipologia)- sql { '.$sql.' }<br>';
        //die();
        
        $rs = $this->ejecutar($sql);
        return $rs;
        
    }
    
    public function getPrestadorSubtipologiaBuscador($param = ''){
        
        $cond = '';
        
        if(!empty($param['CONSULTAR']))
        {
            $cond = 'where p.NOMBRE LIKE "%'.$param['CONSULTAR'].'%" ';
        }
        else
        {
            return false;
        }
        
        $sql = "select
                    p.CODIGO As CODIGO_PRESTADOR,
                    p.COD_ZONA,
                    p.NOMBRE,
                    p.DESCRIPCION,
                    p.DIRECCION,
                    p.TELEFONO,
                    p.CORREO,
                    p.URL,
                    p.UBICACION,
                    p.PRECIO_PROMEDIO,
                    p.HORARIO,
                    p.URL_VIDEO,
                    p.URL_AUDIO
                from $this->_TB_PRESTADOR p $cond";
        
        //echo '<br>(getPrestadorSubtipologia)- sql { '.$sql.' }<br>';
        //die();
        
        $rs = $this->ejecutar($sql);
        return $rs;
        
    }
    
    public function getRgbConvertirAHexadecimal($rgb) {
        
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex; 
     }
     
    public function getObtenerRgbImagen($rutaImagen){
         
        if(empty($rutaImagen)){
            return true;
        }
        
        $rgb_fondo = $this->getObtenerRgbImagenFondo($rutaImagen);
        
        $COLOR_FONDO = $this->getRgbConvertirAHexadecimal($rgb_fondo);
        $valorRgb = ''; 
        $a_valorRgb = array();
        $a_rgb = array();
        
        $imagen = imagecreatefrompng($rutaImagen);
        
        $j = 0;
        
        for($i=60;$i <= 180; $i++)
        {
            $rgb = '';
            
            $rgb = imagecolorat($imagen,$i,$i);
            
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            $valorRgb = ($r + $g + $b);
            
            $a_valorRgb[$j] = $valorRgb;
            $a_rgb[$j]['rgb'] = array($r, $g, $b);
            
            $j++;
            
         }
         
         $clave = array_search(min($a_valorRgb), $a_valorRgb);
       
         return $a_rgb[$clave]['rgb'];
     }
     
    public function getObtenerRgbImagenFondo($rutaImagen){
        
         if(empty($rutaImagen))
         {
            return true; 
         }   
        
         $imagen = imagecreatefrompng($rutaImagen);
         
         $rgb = imagecolorat($imagen, 1, 1);
         
         $r = ($rgb >> 16) & 0xFF;
         $g = ($rgb >> 8) & 0xFF;
         $b = $rgb & 0xFF;

         return array($r, $g, $b);
     }
     
    public function getCrearImagenTriangulo($codigo,$rgb,$nombreImagen){
         
        $imagen  = imagecreatetruecolor(40, 55);
        
        $rojo = imagecolorallocate($imagen, $rgb[0], $rgb[1], $rgb[2]);
        
        $negro = imagecolorallocate($imagen, 0, 0, 0);

        imagefilledpolygon($imagen, array(40, 0, 40, 55, 0, 55), 3, $rojo);

        imagecolortransparent($imagen, $negro);

        //header("Content-type: image/png");
        
        imagepng($imagen, "imagenes/".$nombreImagen."_".$codigo.".png");
       
        imagedestroy($imagen);
        
        $nombreImagen = $nombreImagen."_".$codigo.".png";
         
        return $nombreImagen;
         
     }
     
    public function convertirHexadecimalARgb($hexadecimal) {
        
         $hex = str_replace("#", "", $hexadecimal);

         if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
         } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
         }
         
         $rgb = array($r, $g, $b);
       
         return $rgb;
     }
     
    public function getIdiomas() {

        $sql = 'select CODIGO As CODIGO,NOMBRE As NOMBRE from '.$this->_TB_IDIOMA.' ';
        
        //echo '<br>(getIdiomas)- sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        return $rs;
    }
    
    public function getTraduccionIdioma($param){
        
        $cond = '';
        $rs = '';
        $traduccion = '';
        
        $cond = 'where TABLA= "'.$param['TABLA'].'" And COD_TABLA='.$param['COD_TABLA'].' And COD_IDIOMA='.$param['COD_IDIOMA'].' ';

        $sql = 'select NOMBRE As NOMBRE,DESCRIPCION As DESCRIPCION from '.$this->_TB_TRADUCCION.' '.$cond.' ';

        //echo '<br>(getTraduccionIdioma)- sql { '.$sql.' }<br>';
        //die();

        $rs = $this->ejecutar($sql);

        $a_datos = mysqli_fetch_array($rs); 
        
        //print_r($a_datos);
        //die();
        
        return $a_datos;
        
        
    }
    
    public function getRutaPrestador($param){
        
        $cond = '';
        $rs = '';
        
        $cond = 'where CODIGO_RUTA='.$param['CODIGO'].' ';

        $sql = 'select 
                    p.CODIGO As CODIGO,
                    p.NOMBRE As NOMBRE,
                    REPLACE( REPLACE( REPLACE( AsText(p.UBICACION),"POINT(","" ),")","")," ",",") As UBICACION,
                    p.DESCRIPCION,
                    p.DIRECCION,
                    p.TELEFONO,
                    p.CORREO,
                    p.URL,
                    p.PRECIO_PROMEDIO,
                    p.HORARIO,
                    P.URL_VIDEO,
                    p.URL_AUDIO
                from 
                    '.$this->_TB_RUTA_PRESTADOR.' rp inner join '.$this->_TB_PRESTADOR.' p on rp.CODIGO_PRESTADOR=p.CODIGO '.$cond.' ';
        
        //echo '<br>(getRutaPrestador)- sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);

        return $rs;
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
    
    public function getInformacionPrestador($param = ''){
        
        $cond = '';
        
        if(!empty($param['CODIGO_PRESTADOR']))
        {
            $cond = 'where p.CODIGO= '.$param['CODIGO_PRESTADOR'].' ';
        }
        
        $sql = "select
                   
                from $this->_TB_PRESTADOR p 
                $cond";
        
        echo '<br>(getInformacionPrestador)- sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        
        $a_datos = mysqli_fetch_array($rs); 
        
        return $a_datos;
        
    }
    
    public function getEtiqueta(){
        
        //$cond = '';
        
        $sql = "select
                    CODIGO As CODIGO,
                    NOMBRE As NOMBRE
                from 
                    $this->_TB_ETIQUETA
                where 
                    TABLA='APP_MOVIL' ";
        
        //echo '<br>(getEtiqueta)- sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        
        //$a_datos = mysqli_fetch_array($rs); 
        
        return $rs;
        
    }

}

?>