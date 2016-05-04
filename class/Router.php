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
 * Interpret HTTP request data and call handler
 */
class Router
{
	/// Handler to invoke
	protected $handler;


	/**
	 * Constructor
	 */
	public function __construct($config, IHandler $handler)
	{
		$this->handler = $handler;
	}


	/**
	 * Interpret the HTTP request
	 */
	public function handle($server, $get, $post)
	{
		$smalldb = null;

		// Convert current path to array and to string (result: $path is array, $path_str is string)
		$path = trim(isset($server['PATH_INFO']) ? $server['PATH_INFO'] : '', '/');
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
		$id = $path;

		// Do something useful
		if (!empty($path)) {
			if ($action !== null) {
				if ($server['REQUEST_METHOD'] == 'POST') {
					// Invoke transition
					$args = isset($post['args']) ? $post['args'] : [];
					return $this->handler->invokeTransition($id, $action, $args);
				} else {
					// Show transition
					return $this->handler->checkTransition($id, $action);
				}
			} else if (!empty($get)) {
				// Get view(s)
				return $this->handler->readViews($id, array_keys($get));
			} else {
				// Get properties
				return $this->handler->readState($id);
			}
		} else if (!empty($get)) {
			// Listing
			return $this->handler->listing($get);
		} else {
			// Nothing, just list known types
			return $this->handler->getKnownTypes();
		}
	}

}

