# TYPO3 Extension Utils

> I'm currently not investing very much time into Typo3ExtensionUtils.
> I really hope to merge back czenker's Version 2 (https://github.com/czenker/Typo3ExtensionUtils/tree/dev-2.0) at someday.
> In the meanwhile I'll gladly review and accept any pull requests for Typo3ExtensionUtils.

This is (or should get) a collection of CLI utilities for TYPO3 CMS Extension. Goal is to be able to do common tasks
while developing extensions from the cli. All tools work without a fully functional TYPO3 installation. Actually the
TYPO3 CMS core isn't needed at all for this utilities.

## Installation with wget

	wget http://bit.ly/t3xutils -O t3xutils.phar
	chmod +x t3xutils.phar
	./t3xutils.phar help

## Installation with git

	git clone https://github.com/etobi/Typo3ExtensionUtils.git
	cd Typo3ExtensionUtils/bin/
	chmod +x t3xutils.phar
	./t3xutils.phar help

## Features

* √ "check for updates" and "selfupdate"
* √ Upload an extension by given path to the TER (TYPO3 Extension Repository)
* √ display extension and version informations from TER
* √ download an extension from TER as t3x
* (coming) Delete an extension in a certain version from TER
* (coming) Update the MD5 sums in ext_emconf.php
* (coming) Check extension files against MD5 sums to find modified files
* √ Extract extension from a .t3x file
* √ Create a .t3x from a extension path
* √ List extension files in a .t3x file


### Help

Usage:

	./t3xutils.phar help


### Self-update

	# check if a new version is available
	./t3xutils.phar checkforupdate

	# download and install the new version
	./t3xutils.phar selfupdate


### Upload Extension to TER

Usage:

	./t3xutils.phar upload <typo3.org-username> <typo3.org-password> <extensionKey> "<uploadComment>" <pathToExtension>

Example:

	./t3xutils.phar upload eTobi.de 'mySecretPassword' foobar "Minor Bugfixes and cleanup" /var/www/foobar/typo3conf/ext/foobar/
	
t3xutils *does not* increase the Version string in your ext_emconf by intention. You have to take care of that yourself.


### Display TER informations about an extension

	# fetch and cache TER extension metadata
	./t3xutils.phar updateinfo

	# Display available versions of EXT:foobar
	./t3xutils.phar info foobar

	# Display metadata of EXT:foobar, version 1.2.3
	./t3xutils.phar info foobar 1.2.3


### Fetch extension from TER

	# download EXT:foobar in version 1.2.3 as .t3x file from TER
	./t3xutils.phar fetch foobar 1.2.3


### Extract a .t3x file

	# extract the file 'foobar_1.2.3.t3x' into 'typo3conf/ext/foobar/'
	./t3xutils.phar extract foobar_1.2.3.t3x typo3conf/ext/foobar/


### Create a .t3x file

	# create a 'foobar.t3x' file for the EXT:foobar from the content of 'typo3conf/ext/foobar'
	./t3xutils.phar create foobar typo3conf/ext/foobar foobar.t3x


### Show metadata from .t3x file

	./t3xutils.phar showmetadata ./foobar.t3x

