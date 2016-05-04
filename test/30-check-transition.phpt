--TEST--
Router: check transition
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'GET',
		'PATH_INFO' => '/blogpost/123!edit'
	];
	
	$get = [];

	$post = [];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::checkTransition: [
    [
        "blogpost",
        123
    ],
    "edit"
]

