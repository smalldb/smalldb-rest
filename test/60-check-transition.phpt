--TEST--
Check transition
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	$response = $handler->checkTransition(['blogpost', '1'], 'edit');
	Smalldb\Rest\JsonResponse::writeJson($response);

?>
--EXPECT--
{
    "id": [
        "blogpost",
        1
    ],
    "action": "edit",
    "allowed": true
}

