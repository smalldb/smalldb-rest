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
 * Training dummy for Router testing
 */
class DummyHandler implements IHandler
{

	/**
	 * Log all calls
	 */
	function __call($method, $args)
	{
		echo "$method: ";
		JsonResponse::writeJson($args);
	}


	///@copydoc IHandler::getKnownTypes
	function getKnownTypes()
	{
		$this->__call(__METHOD__, func_get_args());
	}

	///@copydoc IHandler::readState
	function readState($id)
	{
		$this->__call(__METHOD__, func_get_args());
	}


	///@copydoc IHandler::readViews
	function readViews($id, $view_names)
	{
		$this->__call(__METHOD__, func_get_args());
	}


	///@copydoc IHandler::invokeTransition
	function invokeTransition($id, $transition_name, $transition_args)
	{
		$this->__call(__METHOD__, func_get_args());
	}

	///@copydoc IHandler::checkTransition
	function checkTransition($id, $action)
	{
		$this->__call(__METHOD__, func_get_args());
	}


	///@copydoc IHandler::listing
	function listing($filters)
	{
		$this->__call(__METHOD__, func_get_args());
	}

}

