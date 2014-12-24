<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$a_str = explode('imagenesBD','/home/imagenesBD/Tipologia1/Subtipologia1/imgSubtipologia1.png');

print_r($a_str);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
{
    echo '<h1>Este es un servidor que esta usando Windows!</h1>';
} 
else 
{
    echo '<h1>Este es un servidor que no usa Windows!</h1>';
}
