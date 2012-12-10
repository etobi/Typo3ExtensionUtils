#!/usr/bin/env php
<?php

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

$arguments = array_splice($_SERVER['argv'], 1);
var_dump($arguments);
// TODO check arguments

$controller = new \etobi\extensionUtils\Controller\UploadController();
$controller->testAction(
	$arguments[0],
	$arguments[1],
	$arguments[2],
	$arguments[3],
	$arguments[4]
);
