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

try {
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

	// Convert current path to array and to string (result: $path is array, $path_str is string)
	$path = trim(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '', '/');
	$path = ($path == '' ? array() : explode('/', $path));

	// Get '!action'
	$path_tail = end($path);
	if (strpos($path_tail, '!') !== FALSE) {
		list($path_tail, $action, ) = explode('!', $path_tail, 3); // drop extra '!'
		if ($path_tail != '') {
			$path[key($path)] = $path_tail;
		} else {
			unset($path[key($path)]);
		}
	} else {
		$action = null;
	}

	// Do something useful
	$response = array();
	header('Content-Type: application/json; encoding=utf-8');
	if (!empty($path)) {
		$ref = $smalldb->ref($path);
		if ($action !== null) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// Invoke transition
				$response['machine_id'] = $path;
				$response['action'] = $action;
				$response['result'] = call_user_func_array(array($ref, $action), empty($_POST['args']) ? array() : $_POST['args']);
			} else {
				// Show transition
				$response['machine_id'] = $path;
				$response['action'] = $action;
				$response['allowed'] = $ref->machine->isTransitionAllowed($ref, $action);
			}
		} else if (!empty($_GET)) {
			// Get view(s)
			$response['machine_id'] = $path;
			foreach ($_GET as $view => $x) {
				$response[$view] = $ref->$view;
			}
		} else {
			// Get properties
			$response['machine_id'] = $path;
			$response['properties'] = $ref;
		}
	} else if (!empty($_GET)) {
		// Listing
		$listing = $smalldb->createListing($_GET);
		$response['items'] = $listing->fetchAll();
		$response['processed_filters'] = $listing->getProcessedFilters();
	} else {
		// Nothing, just list known types
		$response['known_types'] = $smalldb->getKnownTypes();
	}

	echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK), "\n";

}
catch(\Exception $ex) {
	$class = get_class($ex);
	switch ($class) {
		case 'Smalldb\\StateMachine\\TransitionAccessException':
			$http_code = '403';
			break;
		case 'Smalldb\\StateMachine\\InstanceDoesNotExistException':
			$http_code = '404';
			break;
		default:
			$http_code = '500';
			break;
	}

	header("HTTP/1.0 $http_code Exception");
	header('Content-Type: application/json; encoding=utf-8');
	$response = array(
		'exception' => $class,
		'message' => $ex->getMessage(),
		'code' => $ex->getCode(),
	);
	echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK), "\n";
	error_log($ex);
}

