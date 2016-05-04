--TEST--
Router: no arguments
--FILE--
<?php

	require('init.php');
	$router = new Smalldb\Rest\Router([], new Smalldb\Rest\DummyHandler());
	$router->handle(array(), array(), array());

?>
--EXPECT--
Smalldb\Rest\DummyHandler::getKnownTypes: []

