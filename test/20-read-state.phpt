--TEST--
Router: read state
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'GET',
		'PATH_INFO' => '/blogpost/123'
	];
	
	$get = [];

	$post = [];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::readState: [
    [
        "blogpost",
        123
    ]
]

