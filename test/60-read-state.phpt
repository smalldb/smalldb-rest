--TEST--
Read state
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	echo "== Good ==\n\n";
	$response = $handler->readState(['blogpost', '2']);
	Smalldb\Rest\JsonResponse::writeJson($response);

	try {
		echo "\n== Unknown instance ==\n\n";
		$response = $handler->readState(['blogpost', '100']);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n== Empty ID ==\n\n";
		$response = $handler->readState(null);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n== Incomplete ID ==\n\n";
		$response = $handler->readState(['blogpost']);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n== Unknown machine type ==\n\n";
		$response = $handler->readState(['xyz', '123']);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

?>
--EXPECT--
== Good ==

{
    "id": [
        "blogpost",
        2
    ],
    "properties": {
        "id": 2,
        "title": "Once upon a ...",
        "publishTime": "2016-01-10",
        "isDeleted": 0,
        "state": "published"
    },
    "state": "published"
}

== Unknown instance ==

{
    "exception": "Smalldb\\StateMachine\\InstanceDoesNotExistException",
    "message": "State machine instance not found: 100",
    "code": 0
}

== Empty ID ==

{
    "exception": "Smalldb\\StateMachine\\InvalidReferenceException",
    "message": "Cannot infer machine type: ",
    "code": 0
}

== Incomplete ID ==

{
    "exception": "Smalldb\\StateMachine\\InstanceDoesNotExistException",
    "message": "State machine instance does not exist (null ID).",
    "code": 0
}

== Unknown machine type ==

{
    "exception": "Smalldb\\StateMachine\\InvalidReferenceException",
    "message": "Cannot infer machine type: xyz",
    "code": 0
}

