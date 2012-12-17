<?php

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

$dispatcher = new \etobi\extensionUtils\Dispatcher();

$arguments = array_splice($_SERVER['argv'], 1);
$dispatcher->setCommandCalled($_SERVER['argv'][0]);
$dispatcher->setArguments($arguments);
$dispatcher->run();
