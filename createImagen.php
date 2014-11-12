<?php

$im  = imagecreatetruecolor(40, 55);
$b   = imagecolorallocate($im, 255, 255, 255);
$rojo = imagecolorallocate($im, 255, 0, 0);
$negro = imagecolorallocate($im, 0, 0, 0);

/* Dibujar una línea discontinua, 5 píxeles rojos, 5 píxeles blancos */
$estilo = array($b, $b, $b, $b, $b, $b, $b, $b, $b, $b);
imagesetstyle($im, $estilo);

imagefilledpolygon($im, array(40, 0, 40, 55, 0, 55), 3, $rojo);

//imagefill($im, 0, 0, $rojo);

//imageline($im, 0, 55, 40, 0, IMG_COLOR_STYLED);

imagecolortransparent($im, $negro);

//Dibujar la imagen
header("Content-type: image/png");
//Si la imagen se quiere escribir en un fichero
imagepng($im, "imagenes/triangulo.png");
//Para mostrar la imagen por pantalla (en el navegador)
imagepng($im);
//Destruir la imagen para no dejarla en el servidor
imagedestroy($im);

?>