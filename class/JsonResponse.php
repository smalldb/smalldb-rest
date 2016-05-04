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
 * Send well-formated JSON response
 */
class JsonResponse {

	/**
	 * Send response data when everything is ok.
	 */
	public static function sendData($response_data)
	{
		header('Content-Type: application/json; encoding=utf-8');
		static::writeJson($response_data);
	}


	/**
	 * Send info about thrown exception and set proper HTTP code.
	 */
	public static function sendException(\Exception $ex)
	{
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

		header("HTTP/1.0 $http_code Exception: $class");
		header('Content-Type: application/json; encoding=utf-8');
		static::writeException($ex);
	}


	/**
	 * Render exception as JSON string
	 */
	public static function writeException(\Exception $ex)
	{
		$response = array(
			'exception' => get_class($ex),
			'message' => $ex->getMessage(),
			'code' => $ex->getCode(),
		);
		static::writeJson($response);
	}

	/**
	 * Convert data to JSON and send them to client.
	 */
	public static function writeJson($json_data)
	{
		echo json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), "\n";
	}

}

