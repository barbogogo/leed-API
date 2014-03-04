<?php

require_once('./commonAPI.php');
require_once('./constantAPI.php');
require_once('./auth.php');
require_once('./phpError.php');

$realm = $_SERVER['SERVER_NAME'];

$authError = "";

if(!$myUser && !($myUser = auth_check($realm))) 
{
   auth_request($realm);
}
else
{
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    header('Content-type: application/json');
    $jsonOutput = "{\"error\":{\"id\":\"0\",\"message\":\"no error\"}}\n";
}

$_SESSION['currentUser'] = serialize($myUser);

if($isErrorPHP == true)
{
    echo $msgErrorPHP;
    
    $_SESSION = array();
    session_unset();
    session_destroy();
}
else
{
    echo $jsonOutput;
}

?>
