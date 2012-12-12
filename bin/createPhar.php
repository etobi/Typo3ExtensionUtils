#!/usr/bin/env php
<?php

$phar = new Phar(__DIR__ . '/../bin/extension.phar', 0, 'extension.phar');
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