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
 *
 * @param $base_dir Base directory, where `config.app.json.php` is located.
 * @param $task Select what this PHP script shall do -- see `TASK_*` constants.
 *
 * @note This class is a bit ugly to allow use of various parts from tests. The
 * 	only real public API is the main() method.
 */
class Application
{

	const TASK_INIT = 'init';		///< Initialization only (for custom scripts)
	const TASK_API = 'api';			///< Implement Smalldb JSON REST API
	const TASK_DIAGRAM = 'diagram';		///< State diagram renderer ($_GET params: machine, format (dot, png, pdf, svg))
	const TASK_SELFCHECK = 'selfcheck';	///< Perform a quick self-check to detect most common errors


	/**
	 * Initialize environment
	 *
	 * Usage:
	 *
	 *     require __DIR__."/vendor/autoload.php";
	 *     list($config, $smalldb) = Smalldb\Rest\Application::initialize(__DIR__);
	 *
	 * @return [$config, $smalldb]
	 */
	public static function initialize($base_dir)
	{
		return static::main($base_dir, self::TASK_INIT);
	}


	/**
	 * The main()
	 */
	public static function main($base_dir, $task = self::TASK_API)
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

			// TODO: Replace switch with something nice
			switch ($task) {
				case self::TASK_INIT:
					// Initialization complete, let the rest of the script do its job.
					return [$config, $smalldb];
				case self::TASK_API:
					$handler = static::createHandler($config, $smalldb);
					$router = static::createRouter($config, $handler);
					JsonResponse::sendData($router->handle($_SERVER, $_GET, $_POST));
					break;
				case self::TASK_DIAGRAM:
					static::renderStateMachine($smalldb, $_GET['machine'], $_GET['format']);
					break;
				case self::TASK_SELFCHECK:
					JsonResponse::sendData(static::performSelfCheck($smalldb));
					break;
				default:
					throw new \InvalidArgumentException('Unknown task');
			}
		}
		catch(\Exception $ex) {
			error_log($ex);
			JsonResponse::sendException($ex);
		}
	}


	/**
	 * @name Helper methods
	 *
	 * @note These methods are not public API and they are likely to be changed.
	 * 
	 * @{
	 */

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
		// Create Smalldb backend without context
		$smalldb = new \Smalldb\StateMachine\JsonDirBackend($config['smalldb'], null, 'smalldb');

		// Initialize database connection & query builder
		$flupdo = \Smalldb\Flupdo\Flupdo::createInstanceFromConfig($config['flupdo']);

		// Initialize authenticator
		if (!isset($config['auth']['class'])) {
			throw new InvalidArgumentException('Authenticator not defined. Please set auth.class option.');
		}
		$auth_class = $config['auth']['class'];
		$auth = new $auth_class($config['auth'], $smalldb);

		// Set Smalldb context
		$smalldb->setContext(array(
				'database' => $flupdo,
				'auth' => $auth,
			));

		// Kick up Auth
		$auth->checkSession();

		return $smalldb;
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

	/// @}

	/**
	 * Render state diagram
	 */
	public static function renderStateMachine(\Smalldb\StateMachine\AbstractBackend $smalldb, $machine, $format)
	{
		$dot = $smalldb->getMachine($machine)->exportDot();

		if ($format == 'dot') {
			header('Content-Type: text/plain; encoding=utf8');
			echo $dot;
		} else if ($format == 'png' || $format == 'pdf' || $format == 'svg') {
			$image = FALSE;

			// Check cache
			$dot_hash = md5($dot).'_'.$format;
			if (function_exists('apcu_fetch')) {
				$image = apcu_fetch($dot_hash);
			}

			if ($image === FALSE) {
				// Render diagram using Graphviz
				$p = proc_open('dot -T'.$format, [
						['pipe', 'r'], ['pipe', 'wb'], ['file', 'php://stdout', 'a']
					], $fp);
				fwrite($fp[0], $dot);
				fclose($fp[0]);
				$image = stream_get_contents($fp[1]);
				fclose($fp[1]);
				$err = proc_close($p);

				if ($err != 0) {
					// Failed - drop corrupt image data
					$image = FALSE;
				} else if (function_exists('apcu_store')) {
					// Store image in cache
					apcu_store($dot_hash, $image, 3600);
				}
			}

			if ($image) {
				// Send image
				switch ($format) {
					case 'png': header('Content-Type: image/png'); break;
					case 'svg': header('Content-Type: image/svg+xml'); break;
					case 'pdf': header('Content-Type: application/pdf'); break;
				}
				echo $image;
			}
		} else {
			throw new \InvalidArgumentException('Unknown format');
		}
	}


	/**
	 * Perform self-check.
	 */
	public static function performSelfCheck(\Smalldb\StateMachine\AbstractBackend $smalldb)
	{
		return $smalldb->performSelfCheck();
	}

}

