--TEST--
Check transition
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	echo "Auth class: ", get_class($handler->getSmalldb()->getContext('auth')), "\n\n";

	$response = $handler->checkTransition(['blogpost', '1'], 'edit');
	Smalldb\Rest\JsonResponse::writeJson($response);

?>
--EXPECT--
Auth class: Smalldb\StateMachine\Auth\AllowAllAuth

{
    "id": [
        "blogpost",
        1
    ],
    "action": "edit",
    "allowed": true
}

