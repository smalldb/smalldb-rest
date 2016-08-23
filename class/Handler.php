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
 * Handler, which really invokes Smalldb
 */
class Handler implements IHandler
{
	/// Smalldb to operate on
	protected $smalldb;

	/**
	 * Constructor
	 */
	public function __construct($config, $smalldb)
	{
		$this->smalldb = $smalldb;
	}


	/// @copydoc IHandler::getKnownTypes
	public function getKnownTypes()
	{
		return [
			'types' => $this->smalldb->getKnownTypes(),
		];
	}


	/// @copydoc IHandler::listing
	public function listing($filters)
	{
		$listing = $this->smalldb->createListing($filters);
		return [
			'items' => array_values($listing->fetchAll()),	// Order of object's properties not guaranteed in JS
			'processed_filters' => $listing->getProcessedFilters(),
		];
	}


	public function readState($id)
	{
		$ref = $this->smalldb->ref($id);
		return [
			'id' => $id,
			'properties' => $ref->properties,
			'state' => $ref->state,
		];
	}

	public function readViews($id, $view_names)
	{
		$ref = $this->smalldb->ref($id);
		$response = [
			'id' => $id,
		];
		foreach ($view_names as $view) {
			$response[$view] = $ref->$view;
		}
		return $response;
	}


	public function checkTransition($id, $action)
	{
		$ref = $this->smalldb->ref($id);
		return [
			'id' => $id,
			'action' => $action,
			'allowed' => $ref->machine->isTransitionAllowed($ref, $action),
		];
	}

	public function invokeTransition($id, $action, $args)
	{
		$ref = $this->smalldb->ref($id);
		return [
			'id' => $id,
			'action' => $action,
			'result' => call_user_func_array(array($ref, $action), empty($args) ? [] : $args),
		];
	}

}

