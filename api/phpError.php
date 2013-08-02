<?php

$isErrorPHP  = false;
$msgErrorPHP = "";

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) 
{
    $errortype = array(1=>"Erreur", 
            2=>"Alerte", 
            4=>"Erreur d'analyse", 
            8=>"Note",
            16=>"Code Error", 
            32=>"Code Warning", 
            64=>"Zend Scripting Engine Error",
            128=>"Zend Scripting Engine Warning",
            256=>"Erreur spécifique",
            512=>"Alerte spécifique",
            1024=>"Note spécifique",
            2048=>"Note inconnue"
            );
    
    $erreur = $errortype[$errno]."[".$errno."]".$errmsg. "- ".$filename."(l".$linenum.")";
    
    echo "{\"error\":{\"id\":\"3\",\"message\":\"PHP error: ".$erreur."\"}}\n";
    // global $isErrorPHP = true;
}

unset($old_error_handler);
$old_error_handler = set_error_handler("userErrorHandler");

?>