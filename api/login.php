<?php

require_once('../../common.php');
require_once('./constantAPI.php');
require_once('./auth.php');


$realm = $_SERVER['SERVER_NAME'];

if(!$myUser && !($myUser = auth_check($realm))) {
   auth_request($realm);
}

$_SESSION['currentUser'] = serialize($myUser);
 
?>
