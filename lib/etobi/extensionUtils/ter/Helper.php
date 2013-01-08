<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
namespace etobi\extensionUtils\ter;

/**
 * Helper functions.
 * Taken from t3lib_div from the TYPO3 CMS 4.5 Core.
 */
class Helper {

	/**
	 * Check for item in list
	 * Check if an item exists in a comma-separated list of items.
	 *
	 * @param    string        comma-separated list of items (string)
	 * @param    string        item to check for
	 * @return    boolean        true if $item is in $list
	 */
	public static function inList($list, $item) {
		return (strpos(',' . $list . ',', ',' . $item . ',') !== FALSE ? TRUE : FALSE);
	}

	/**
	 * Recursively gather all files and folders of a path.
	 * Usage: 5
	 *
	 * @param    array        $fileArr: Empty input array (will have files added to it)
	 * @param    string        $path: The path to read recursively from (absolute) (include trailing slash!)
	 * @param    string        $extList: Comma list of file extensions: Only files with extensions in this list (if applicable) will be selected.
	 * @param    boolean        $regDirs: If set, directories are also included in output.
	 * @param    integer        $recursivityLevels: The number of levels to dig down...
	 * @param string        $excludePattern: regex pattern of files/directories to exclude
	 * @return    array        An array with the found files/directories.
	 */
	public static function getAllFilesAndFoldersInPath(array $fileArr, $path, $extList = '', $regDirs = 0, $recursivityLevels = 99, $excludePattern = '') {
		if ($regDirs) {
			$fileArr[] = $path;
		}
		$fileArr = array_merge($fileArr, self::getFilesInDir($path, $extList, 1, 1, $excludePattern));

		$dirs = self::get_dirs($path);
		if (is_array($dirs) && $recursivityLevels > 0) {
			foreach ($dirs as $subdirs) {
				if ((string)$subdirs != '' && (!strlen($excludePattern) || !preg_match('/^' . $excludePattern . '$/', $subdirs))) {
					$fileArr = self::getAllFilesAndFoldersInPath($fileArr, $path . $subdirs . '/', $extList, $regDirs, $recursivityLevels - 1, $excludePattern);
				}
			}
		}
		return $fileArr;
	}

	/**
	 * Returns an array with the names of files in a specific path
	 * Usage: 18
	 *
	 * @param    string        $path: Is the path to the file
	 * @param    string        $extensionList is the comma list of extensions to read only (blank = all)
	 * @param    boolean        If set, then the path is prepended the filenames. Otherwise only the filenames are returned in the array
	 * @param    string        $order is sorting: 1= sort alphabetically, 'mtime' = sort by modification time.
	 * @param    string        A comma seperated list of filenames to exclude, no wildcards
	 * @return    array        Array of the files found
	 */
	public static function getFilesInDir($path, $extensionList = '', $prependPath = 0, $order = '', $excludePattern = '') {

		// Initialize variabels:
		$filearray = array();
		$sortarray = array();
		$path = rtrim($path, '/');

		// Find files+directories:
		if (@is_dir($path)) {
			$extensionList = strtolower($extensionList);
			$d = dir($path);
			if (is_object($d)) {
				while ($entry = $d->read()) {
					if (@is_file($path . '/' . $entry)) {
						$fI = pathinfo($entry);
						$key = md5($path . '/' . $entry); // Don't change this ever - extensions may depend on the fact that the hash is an md5 of the path! (import/export extension)
						if ((!strlen($extensionList) || self::inList($extensionList, strtolower($fI['extension']))) && (!strlen($excludePattern) || !preg_match('/^' . $excludePattern . '$/', $entry))) {
							$filearray[$key] = ($prependPath ? $path . '/' : '') . $entry;
							if ($order == 'mtime') {
								$sortarray[$key] = filemtime($path . '/' . $entry);
							} elseif ($order) {
								$sortarray[$key] = $entry;
							}
						}
					}
				}
				$d->close();
			} else {
				return 'error opening path: "' . $path . '"';
			}
		}

		// Sort them:
		if ($order) {
			asort($sortarray);
			$newArr = array();
			foreach ($sortarray as $k => $v) {
				$newArr[$k] = $filearray[$k];
			}
			$filearray = $newArr;
		}

		// Return result
		reset($filearray);
		return $filearray;
	}

	/**
	 * Returns an array with the names of folders in a specific path
	 * Will return 'error' (string) if there were an error with reading directory content.
	 *
	 * @param    string        Path to list directories from
	 * @return    array        Returns an array with the directory entries as values. If no path, the return value is nothing.
	 */
	public static function get_dirs($path) {
		if ($path) {
			if (is_dir($path)) {
				$dir = scandir($path);
				$dirs = array();
				foreach ($dir as $entry) {
					if (is_dir($path . '/' . $entry) && $entry != '..' && $entry != '.') {
						$dirs[] = $entry;
					}
				}
			} else {
				$dirs = 'error';
			}
		}
		return $dirs;
	}

	public static function getEmConf($extensionKey, $path) {
		$EM_CONF = array();
		$_EXTKEY = $extensionKey;
		require $path . 'ext_emconf.php';
		$emConf = $EM_CONF[$_EXTKEY];
		if (empty($emConf['title']) || empty($emConf['version'])) {
			throw new \Exception('Invalid $EM_CONF');
		}
		return $emConf;
	}

	/**
	 * @param $path
	 * @param string $excludeForPackaging
	 * @param int $maxUploadSize
	 * @return array
	 * @throws \Exception
	 */
	public static function getExtensionFilesData($path, $excludeForPackaging = '(CVS|\..*|.*~|.*\.bak)', $maxUploadSize = 31457280) {
		$path = rtrim($path, '/') . '/';

		if (!is_dir($path) || !is_readable($path)) {
			throw new \Exception('Cant read "' . $path .'"');
		}

		$fileArr = array();
		$fileArr = self::getAllFilesAndFoldersInPath($fileArr, $path, '', 0, 99, $excludeForPackaging);

		$totalSize = 0;
		foreach ($fileArr as $filePath) {
			$totalSize += filesize($filePath);
		}

		if ($totalSize >= $maxUploadSize) {
			throw new \Exception('Maximum upload size exceeded (' . $maxUploadSize . ').');
		}

		$filesData = array();
		foreach ($fileArr as $filePath) {
			$fileName = substr($filePath, strlen($path));
			if ($fileName != 'ext_emconf.php') { // This file should be dynamically written...
				$content = file_get_contents($filePath);
				$filesData[utf8_encode($fileName)] = array(
					'name' => utf8_encode($fileName),
					'size' => intval(filesize($filePath)),
					'modificationTime' => intval(filemtime($filePath)),
					'isExecutable' => intval(is_executable($filePath)),
					'content' => $content,
					'contentMD5' => md5($content),
					'content_md5' => md5($content),
				);
			}
		}
		return $filesData;
	}
}