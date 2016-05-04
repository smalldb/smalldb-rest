--TEST--
Read views
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	echo "== Good ==\n\n";
	$response = $handler->readViews(['blogpost', '2'], ['url', 'state', 'properties']);
	Smalldb\Rest\JsonResponse::writeJson($response);

	try {
		echo "\n\n== Unknown view ==\n\n";
		$response = $handler->readViews(['blogpost', '2'], ['url', 'state', 'bla...bla']);
		Smalldb\Rest\JsonResponse::writeJson($response);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n\n== Unknown machine - no properties ==\n\n";
		$response = $handler->readViews(['blogpost', '100'], ['url', 'state']);
		Smalldb\Rest\JsonResponse::writeJson($response);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n\n== Unknown machine - with properties ==\n\n";
		$response = $handler->readViews(['blogpost', '100'], ['url', 'state', 'properties']);
		Smalldb\Rest\JsonResponse::writeJson($response);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n\n== Unknown type ==\n\n";
		$response = $handler->readViews(['xyz', '123'], ['url', 'state', 'properties']);
		Smalldb\Rest\JsonResponse::writeJson($response);
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
    "url": "/blogpost/2",
    "state": "published",
    "properties": {
        "id": 2,
        "title": "Once upon a ...",
        "publishTime": "2016-01-10",
        "isDeleted": 0,
        "state": "published"
    }
}


== Unknown view ==

{
    "exception": "Smalldb\\StateMachine\\InvalidArgumentException",
    "message": "Unknown view \"bla...bla\" requested on machine \"blogpost\".",
    "code": 0
}


== Unknown machine - no properties ==

{
    "id": [
        "blogpost",
        100
    ],
    "url": "/blogpost/100",
    "state": ""
}


== Unknown machine - with properties ==

{
    "exception": "Smalldb\\StateMachine\\InstanceDoesNotExistException",
    "message": "State machine instance not found: 100",
    "code": 0
}


== Unknown type ==

{
    "exception": "Smalldb\\StateMachine\\InvalidReferenceException",
    "message": "Cannot infer machine type: xyz",
    "code": 0
}

