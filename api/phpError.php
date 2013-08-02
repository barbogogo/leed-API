<?php

$constant = "<?php
define('PLUGIN_ENABLED','1');
?>";

file_put_contents('plugins/api/constantAPI.php', $constant);

?>