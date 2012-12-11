#!/usr/bin/env php
<?php

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

$arguments = array_splice($_SERVER['argv'], 1);

if (count($arguments) !== 5) {
	echo 'Usage: ' .
			$_SERVER['argv'][0] .
			' <typo3.org-username> <typo3.org-password> <extensionKey> "<uploadComment>" <pathToExtension>' .
			chr(10);
	exit(1);
}

$controller = new \etobi\extensionUtils\Controller\UploadController();
$success = $controller->uploadAction(
	$arguments[0],
	$arguments[1],
	$arguments[2],
	$arguments[3],
	$arguments[4]
);

exit($success ? 0 : 2);