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
    
    $phpVersion = phpversion();
    $jsonVersion = phpversion('json');
    $mysqlVersion = phpversion('mysql');
    
    $erreur  = "<h1>PHP Error</h1>";
    $erreur .= "<p>".$errortype[$errno]."[".$errno."]".$errmsg. "- ".$filename."(l.".$linenum.")</p>";
    $erreur .= "<p>Your server configuration:</p>";
    $erreur .= "<ul>";
    $erreur .= "<li>PHP Version: ".$phpVersion."</li>";
    $erreur .= "<li>JSON Version: ".$jsonVersion."</li>";
    $erreur .= "<li>MySQL Version: ".$mysqlVersion."</li>";
    $erreur .= "</ul>";
    
    $GLOBALS["msgErrorPHP"] = "{\"error\":{\"id\":\"3\",\"message\":\"".$erreur."\"}}\n";
    $GLOBALS["isErrorPHP"] = true;
}

unset($old_error_handler);
$old_error_handler = set_error_handler("userErrorHandler");

?>