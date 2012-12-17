<?php

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

try {
	$dispatcher = new \etobi\extensionUtils\Dispatcher();

	$arguments = array_splice($_SERVER['argv'], 1);
	$dispatcher->setCommandCalled($_SERVER['argv'][0]);
	$dispatcher->setArguments($arguments);
	$dispatcher->run();
} catch(\Exception $e) {
	echo 'Exception: '. $e->getMessage() . chr(10);
}