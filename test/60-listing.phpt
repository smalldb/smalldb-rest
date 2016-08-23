--TEST--
Listing
--FILE--
<?php

	require('init.php');
	$handler = createHandler();

	echo "== Good ==\n\n";
	$response = $handler->listing(['type' => 'blogpost']);
	Smalldb\Rest\JsonResponse::writeJson($response);

	try {
		echo "\n== Unknown machine type ==\n\n";
		$response = $handler->listing(['type' => 'xyz']);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

	try {
		echo "\n== Missing type ==\n\n";
		$response = $handler->listing([]);
	}
	catch(\Exception $ex) {
		Smalldb\Rest\JsonResponse::writeException($ex);
	}

?>
--EXPECT--
== Good ==

{
    "items": [
        {
            "state": "published",
            "id": 1,
            "title": "About ...",
            "publishTime": "2016-02-20",
            "isDeleted": 0
        },
        {
            "state": "published",
            "id": 2,
            "title": "Once upon a ...",
            "publishTime": "2016-01-10",
            "isDeleted": 0
        },
        {
            "state": "published",
            "id": 3,
            "title": "How to ...",
            "publishTime": "2016-03-30",
            "isDeleted": 0
        }
    ],
    "processed_filters": {
        "_count": 3
    }
}

== Unknown machine type ==

{
    "exception": "Smalldb\\StateMachine\\InvalidArgumentException",
    "message": "Machine type \"xyz\" not found.",
    "code": 0
}

== Missing type ==

{
    "exception": "ErrorException",
    "message": "Undefined index: type",
    "code": 0
}

