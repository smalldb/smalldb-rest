<?php
/*
 * Copyright (c) 2016, Josef Kufner  <josef@kufner.cz>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

// Throw exceptions on all errors
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
	if (error_reporting()) {
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
});

// Load Composer's class loader
require __DIR__."/vendor/autoload.php";

// Load configuration
$config_defaults = json_decode(file_get_contents(__DIR__.'/config.json.php'), TRUE);
if (!$config_defaults) {
	throw new \Exception('Default config file is broken.');
}
$config_local = json_decode(file_get_contents(__DIR__.'/config.local.json.php'), TRUE);
if (!$config_local) {
	throw new \Exception('Local config file is broken.');
}
$config = array_replace_recursive($config_defaults, $config_local);

// Initialize database connection & query builder
$flupdo = \Flupdo\Flupdo\Flupdo::createInstanceFromConfig($config['flupdo']);

// Initialize authenticator
$auth = null; // TODO

// Initialize Smalldb
$smalldb = new \Smalldb\StateMachine\JsonDirBackend($config['smalldb'], array(
		'database' => $flupdo,
		'auth' => $auth
	), 'smalldb');

// Do something useful
echo "Ready.";

