--TEST--
Invoke transition
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	echo "== Before ==\n\n";
	$response = $handler->readState(['blogpost', '1']);
	Smalldb\Rest\JsonResponse::writeJson($response);

	echo "\n== Transition ==\n\n";
	$response = $handler->invokeTransition(['blogpost', '1'], 'edit', [['title' => 'Hello world']]);
	Smalldb\Rest\JsonResponse::writeJson($response);

	echo "\n== After ==\n\n";
	$response = $handler->readState(['blogpost', '1']);
	Smalldb\Rest\JsonResponse::writeJson($response);

?>
--EXPECT--
== Before ==

{
    "id": [
        "blogpost",
        1
    ],
    "properties": {
        "id": 1,
        "title": "About ...",
        "publishTime": "2016-02-20",
        "isDeleted": 0,
        "state": "published"
    },
    "state": "published"
}

== Transition ==

Query:
	UPDATE `blogpost`
	SET `title` = ?
	WHERE (`blogpost`.`id` = ?)

{
    "id": [
        "blogpost",
        1
    ],
    "action": "edit",
    "result": true
}

== After ==

{
    "id": [
        "blogpost",
        1
    ],
    "properties": {
        "id": 1,
        "title": "Hello world",
        "publishTime": "2016-02-20",
        "isDeleted": 0,
        "state": "published"
    },
    "state": "published"
}

