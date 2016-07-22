--TEST--
Get known types
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	$response = $handler->getKnownTypes();

	Smalldb\Rest\JsonResponse::writeJson($response);

?>
--EXPECT--
{
    "types": [
        "blogpost",
        "session",
        "user"
    ]
}

