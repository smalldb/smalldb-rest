--TEST--
Router: invoke transition with arguments
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());

	$server = [
		'REQUEST_METHOD' => 'POST',
		'PATH_INFO' => '/blogpost/123!edit'
	];
	
	$get = [];

	$post = [
		'args' => [
			[
				'title' => 'Some title',
				'text' => 'Lorem ipsum ...',
			]
		]
	];

	$router->handle($server, $get, $post);

?>
--EXPECT--
Smalldb\Rest\DummyHandler::invokeTransition: [
    [
        "blogpost",
        123
    ],
    "edit",
    [
        {
            "title": "Some title",
            "text": "Lorem ipsum ..."
        }
    ]
]


