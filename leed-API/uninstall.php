<?php

$constant = "<?php
define('PLUGIN_ENABLED','0');
?>";

file_put_contents('plugins/api/constantAPI.php', $constant);

?>