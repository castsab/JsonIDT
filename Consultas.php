<?php

include_once 'Conexion.php';

class Consultas extends Conexion {

    public $_conexion = '';

    public function __construct() {
        parent::__construct();
    }

    public function getClasificaciones() {

        $sql = 'select CODIGO As CODIGO,NOMBRE As NOMBRE from clasificacion';
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
                    tipologia
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
                    subtipologia
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
                    PUNTO_MAXIMO As PUNTO_MAXIMO,
                    backgroundColor As backgroundColor
                from 
                    zona
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
                    t.TIENE_SUBTIPOLOGIA
                from 
                    zona_tipologia zt 
                    inner JOIN tipologia t on zt.CODIGO_TIPOLOGIA=t.CODIGO
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
            $sql = 'select * from imagenes where CODIGO_PRESTADOR='.$param['CODIGO_PRESTADOR'].' limit 1';
            
            //echo '<br>(getImagenPrestador)- sql { '.$sql.' }<br>';
        
            $rs = $this->ejecutar($sql);
            
            $data = mysqli_fetch_array($rs); 
            
            $img_prestador = $data[1];
            
        }

       
        return $img_prestador;
    }
    
    public function getPrestadorSubtipologia($param = ''){
        
        $cond = '';
        
        if(!empty($param['CODIGO_SUBTIPOLOGIA']))
        {
            $cond = 'where s.CODIGO= '.$param['CODIGO_SUBTIPOLOGIA'].' ';
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
                    p.HORARIO
                from subtipologia s inner join prestador_subtipologia ps on s.CODIGO=ps.CODIGO_SUBTIPOLOGIA
                                    inner join prestador p on p.CODIGO=ps.CODIGO_PRESTADOR 
                $cond";
        
        //echo '<br>(getPrestadorSubtipologia)- sql { '.$sql.' }<br>';
        
        $rs = $this->ejecutar($sql);
        return $rs;
        
    }
    
    function getRgbConvertirAHexadecimal($rgb) {
        
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex; 
     }
     
     function getObtenerRgbImagen($rutaImagen)
     {
         
        $rgb_fondo = $this->getObtenerRgbImagenFondo($rutaImagen);
        $COLOR_FONDO = $this->getRgbConvertirAHexadecimal($rgb_fondo);
         
        
        $imagen = imagecreatefrompng($rutaImagen);
        
        for($i=80;$i <= 180; $i++)
        {
            $rgb = '';
            
            $rgb = imagecolorat($imagen,$i,$i);
            
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            $COLOR_ICONO = $this->getRgbConvertirAHexadecimal(array($r, $g, $b));
            
            /******************************/
            if($COLOR_FONDO != $COLOR_ICONO)
            {
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                return array($r, $g, $b);
            }
            /******************************/
            
         }
         
         $r = ($rgb >> 16) & 0xFF;
         $g = ($rgb >> 8) & 0xFF;
         $b = $rgb & 0xFF;

         return array($r, $g, $b);
     }
     
     function getObtenerRgbImagenFondo($rutaImagen)
     {
         $imagen = imagecreatefrompng($rutaImagen);
         $rgb = imagecolorat($imagen, 1, 1);
         
         $r = ($rgb >> 16) & 0xFF;
         $g = ($rgb >> 8) & 0xFF;
         $b = $rgb & 0xFF;

         return array($r, $g, $b);
     }

}

?>
