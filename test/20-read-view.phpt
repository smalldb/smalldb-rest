--TEST--
Router: read view
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'GET',
		'PATH_INFO' => '/blogpost/123'
	];
	
	$get = [
		'url' => null,
		'state' => null,
	];

	$post = [];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::readViews: [
    [
        "blogpost",
        123
    ],
    [
        "url",
        "state"
    ]
]

