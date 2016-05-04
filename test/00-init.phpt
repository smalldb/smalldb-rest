--TEST--
Initialization
--FILE--
<?php
	require('init.php');
	echo class_exists('Smalldb\Rest\Application') ? 'ok' : 'fail';
?>
--EXPECT--
ok

