<?php

require_once('../../common.php');
require_once('./auth.php');

$_SESSION = array();
session_unset();
session_destroy();

// force to "unregister" on client side
auth_request($_SERVER['SERVER_NAME']);

?>
