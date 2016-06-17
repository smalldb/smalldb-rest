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

namespace Smalldb\Rest;

/**
 * The REST API application
 *
 * Simply call `Application::main(__DIR__)` and it should work.
 */
class Application
{

	/**
	 * Load configuration from three files (defaults, app, local) and merge it.
	 */
	public static function loadConfig($base_dir)
	{
		$config_defaults = json_decode(file_get_contents(__DIR__.'/../config.json.php'), TRUE);
		if (!$config_defaults) {
			throw new \Exception('Default config file is broken.');
		}
		$config_app = json_decode(file_get_contents($base_dir.'/config.app.json.php'), TRUE);
		if (!$config_app) {
			throw new \Exception('App config file is broken.');
		}
		if (file_exists($base_dir.'/config.local.json.php')) {
			$config_local = json_decode(file_get_contents($base_dir.'/config.local.json.php'), TRUE);
			if (!$config_local) {
				throw new \Exception('Local config file is broken.');
			}
		} else {
			$config_local = [];
		}
		return array_replace_recursive($config_defaults, $config_app, $config_local);
	}


	/**
	 * Create and initialize Smalldb, including Flupdo and Auth objects
	 */
	public static function createSmalldb($config)
	{
		// Initialize database connection & query builder
		$flupdo = \Smalldb\Flupdo\Flupdo::createInstanceFromConfig($config['flupdo']);

		// Initialize authenticator
		$auth = null; // TODO

		// Initialize Smalldb
		return new \Smalldb\StateMachine\JsonDirBackend($config['smalldb'], array(
				'database' => $flupdo,
				'auth' => $auth
			), 'smalldb');
	}


	/**
	 * Create API handler
	 */
	public static function createRouter($config, $handler)
	{
		return new Router($config, $handler);
	}


	/**
	 * Create handler, which processes all API requests from router.
	 */
	public static function createHandler($config, $smalldb)
	{
		return new Handler($config, $smalldb);
	}


	/**
	 * The main()
	 */
	public static function main($base_dir)
	{
		// Throw exceptions on all errors
		set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
			if (error_reporting()) {
				throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
		});

		try {
			$config = static::loadConfig($base_dir);
			$smalldb = static::createSmalldb($config);
			$handler = static::createHandler($config, $smalldb);
			$router = static::createRouter($config, $handler);
			JsonResponse::sendData($router->handle($_SERVER, $_GET, $_POST));
		}
		catch(\Exception $ex) {
			error_log($ex);
			JsonResponse::sendException($ex);
		}
	}

}

