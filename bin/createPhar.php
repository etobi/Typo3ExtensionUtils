#!/usr/bin/env php
<?php

// make sure it doesn't exist
$pharFilepath = __DIR__ . '/../bin/t3xutils.phar';
@unlink($pharFilepath);
try {
	$phar = new Phar($pharFilepath, 0, 't3xutils.phar');
	$phar->buildFromDirectory(
		__DIR__ . '/..',
		'/(bin|lib)\/.*\.php/'
	);
	$phar->setStub(
		'#!/usr/bin/env php
<?php
Phar::mapPhar("t3xutils.phar");
require "phar://t3xutils.phar/bin/t3xutils.php";
__HALT_COMPILER();
?>');
} catch (Exception $e) {
    echo 'Could not create and/or modify phar:', $e;
}