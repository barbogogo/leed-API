<?php

$key = genererKeyAPI();

$constant = "<?php
define('PLUGIN_ENABLED','1');
define('KEY','".$key."');
?>";

file_put_contents('plugins/api/constantAPI.php', $constant);

function genererKeyAPI()
{
	$j = 0;
	$passwd = "";
	while($j!=8)
	{
		$i=rand(48,122);
		if($i>47 && $i<58 || $i>96 && $i<123 || $i>65 && $i<90)
		{
			$i = chr($i);
			$passwd .= $i;
			$j++;
		}
	}
	return $passwd;
}

?>