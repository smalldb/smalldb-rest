--TEST--
Router: invoke transition
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'POST',
		'PATH_INFO' => '/blogpost/123!publish'
	];
	
	$get = [];

	$post = [];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::invokeTransition: [
    [
        "blogpost",
        123
    ],
    "publish",
    []
]

