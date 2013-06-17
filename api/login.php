<?php

require_once('../../common.php');
require_once('./constantAPI.php');
require_once('./auth.php');


$realm = $_SERVER['SERVER_NAME'];

if(PLUGIN_ENABLED == 1)
{
    if(!$myUser && !($myUser = auth_check($realm))) 
    {
       auth_request($realm);
    }
    else
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        header('Content-type: application/json');
        echo "{\"error\":{\"id\":\"0\",\"message\":\"no error\"}}\n";
    }

    $_SESSION['currentUser'] = serialize($myUser);
}
else
{
    // Error#1: plugin disable
    echo "{\"error\":{\"id\":\"1\",\"message\":\"API disabled\"}}\n";
}

?>
