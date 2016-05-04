--TEST--
Router: listing
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'GET',
	];
	
	$get = [
		'filter' => [
			'type' => 'blogpost',
		]
	];

	$post = [];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::listing: [
    {
        "filter": {
            "type": "blogpost"
        }
    }
]

