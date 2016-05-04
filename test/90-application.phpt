--TEST--
Application
--FILE--
<?php

	require('init.php');

	chdir(__DIR__);
	Smalldb\Rest\Application::main('./example');

?>
--EXPECT--
{
    "types": [
        "blogpost"
    ]
}

