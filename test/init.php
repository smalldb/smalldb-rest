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
require dirname(__DIR__)."/vendor/autoload.php";

// Helper function to get Handler instance
function createHandler()
{
	chdir(__DIR__);

	// Load configuration
	$config = Smalldb\Rest\Application::loadConfig('./example');

	// Use temporary database & update configuration
	$config['flupdo']['database'] = ':memory:';

	// Initialize Smalldb
	$smalldb = Smalldb\Rest\Application::createSmalldb($config);

	// Get Flupdo and populate database
	$context = $smalldb->getContext();
	$flupdo = $context['database'];
	$flupdo->beginTransaction();
	$flupdo->query("
		CREATE TABLE \"blogpost\" (
			`id`    INTEGER NOT NULL,
			`title` TEXT NOT NULL,
			`publishTime`   INTEGER NOT NULL,
			`isDeleted`     INTEGER NOT NULL DEFAULT 0,
			PRIMARY KEY(id)
		) WITHOUT ROWID;
	");
	$flupdo->query("INSERT INTO `blogpost` VALUES (1,'About ...','2016-02-20',0);");
	$flupdo->query("INSERT INTO `blogpost` VALUES (2,'Once upon a ...','2016-01-10',0);");
	$flupdo->query("INSERT INTO `blogpost` VALUES (3,'How to ...','2016-03-30',0);");
	$flupdo->commit();

	// Initialize Handler
	return Smalldb\Rest\Application::createHandler($config, $smalldb);
}

