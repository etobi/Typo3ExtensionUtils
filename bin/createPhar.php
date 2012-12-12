#!/usr/bin/env php
<?php

// make sure it doesn't exist
$pharFilepath = __DIR__ . '/../bin/extension.phar';
@unlink($pharFilepath);
try {
	$phar = new Phar($pharFilepath, 0, 'extension.phar');
	$phar->buildFromDirectory(
		__DIR__ . '/..',
		'/(bin|lib)\/.*\.php/'
	);
	$phar->setStub(
		'#!/usr/bin/env php
<?php
Phar::mapPhar("extension.phar");
require "phar://extension.phar/bin/extension.php";
__HALT_COMPILER();
?>');
} catch (Exception $e) {
    echo 'Could not create and/or modify phar:', $e;
}