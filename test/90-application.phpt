--TEST--
Application
--FILE--
<?php

	require('init.php');

	chdir(__DIR__.'/example');
	Smalldb\Rest\Application::main('./');

?>
--EXPECT--
{
    "types": [
        "blogpost",
        "session",
        "user"
    ]
}

