--TEST--
Initialize Smalldb
--FILE--
<?php

	require('init.php');

	$handler = createHandler();
	echo "Handler: ", get_class($handler), "\n";

?>
--EXPECT--
Handler: Smalldb\Rest\Handler

