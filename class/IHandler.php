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
 * Operations invokable by router
 */
interface IHandler
{

	/**
	 * Get known state machine types
	 */
	function getKnownTypes();

	/**
	 * Read state of state machine
	 */
	function readState($id);

	/**
	 * Read state of state machine
	 */
	function readViews($id, $view_names);

	/**
	 * Check if transition is available
	 */
	function checkTransition($id, $action);

	/**
	 * Invoke transition of state machine
	 */
	function invokeTransition($id, $action, $args);

	/**
	 * List state machines matching $filters
	 */
	function listing($filters);

}

