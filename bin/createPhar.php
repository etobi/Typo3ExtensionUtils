#!/usr/bin/env php
<?php

// make sure it doesn't exist
$pharFilepath = __DIR__ . '/../bin/t3xutils.phar';
@unlink($pharFilepath);
try {
	$command = 'git ls-remote . HEAD';
	$output = exec($command);
	if ($output) {
		list($sha1) = explode("\t", $output, 2);
	} else {
		throw new \Exception('Cant get current SHA1');
	}

	file_put_contents('pharVersion.txt', $sha1);

	$phar = new Phar($pharFilepath, 0, 't3xutils.phar');
	$phar->buildFromDirectory(
		__DIR__ . '/..',
		'/(bin|lib)\/.*\.php/'
	);
	echo 'create phar for "' . $sha1 .'"' . chr(10);
	$phar->setStub(
		'#!/usr/bin/env php
<?php
Phar::mapPhar("t3xutils.phar");
define("T3XUTILS_VERSION", "' . $sha1 . '");
define("T3XUTILS_TIMESTAMP", "' . date('c') . '");
require "phar://t3xutils.phar/bin/t3xutils.php";
__HALT_COMPILER();
?>');

	chmod($pharFilepath, 0777 & ~umask());
} catch (Exception $e) {
    echo 'Could not create and/or modify phar:', $e;
}