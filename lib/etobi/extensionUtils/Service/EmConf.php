<?php

namespace etobi\extensionUtils\Service;

/**
 * reader and writer for ext_emconf.php
 *
 * the class is supposed to be unobtrusive. So if you have non-standard keys or strange
 * values the class will try to keep them in a later rewrite if you not explicitly ask
 * it to change the value.
 */
class EmConf {

    const CATEGORY_BACKEND = 'be';
    const CATEGORY_MODULE = 'module';
    const CATEGORY_FRONTEND = 'fe';
    const CATEGORY_PLUGIN = 'plugin';
    const CATEGORY_OTHER = 'misc';
    const CATEGORY_SERVICE = 'services';
    const CATEGORY_TEMPLATE = 'templates';
    const CATEGORY_DOCUMENTATION = 'doc';
    const CATEGORY_EXAMPLE = 'example';

    const STATE_ALPHA = 'alpha';
    const STATE_BETA = 'beta';
    const STATE_STABLE = 'stable';
    const STATE_EXPERIMENTAL = 'experimental';
    const STATE_TEST = 'test';
    const STATE_OBSOLETE = 'obsolete';
    const STATE_EXCLUDE_FROM_UPDATES = 'excludeFromUpdates';
    const STATE_NOT_AVAILABLE = 'n/a';

    const PRIORITY_TOP = 'top';
    const PRIORITY_BOTTOM = 'bottom';
    const PRIORITY_DEFAULT = '';

    protected $emconf = array();
    protected $comment = '';
    protected $fileName = NULL;

    public function readFile($fileName) {
        if(!file_exists($fileName)) {
            throw new \InvalidArgumentException(sprintf('File "%s" does not exist', $fileName));
        }
        if(!is_file($fileName)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a file', $fileName));
        }
        if(!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not readable', $fileName));
        }

        $this->readConfigurationArray($fileName);
        $this->readComment($fileName);
        $this->fileName = $fileName;
    }

    protected function readConfigurationArray($fileName) {
        $EM_CONF = array();
        $_EXTKEY = 'foobar';
        require $fileName;
        $this->emconf = $EM_CONF[$_EXTKEY];
    }

    protected function readComment($fileName) {
        $fh = fopen($fileName, 'r');
        // variable to mimic a flip flop (from PERL)
        $started = FALSE;
        $this->comment = '';
        while($line = fgets($fh)) {
            // match any line with a block comment
            if(preg_match('|^\s*/?\*|',  $line) > 0) {
                $started = TRUE;
                $this->comment .= $line;
            } elseif($started === TRUE) {
                return;
            }
        }
    }

    public function readString($string) {
        $fileName = tempnam(sys_get_temp_dir(), 'test');
        $success = file_put_contents($fileName, $string);
        if(!$success) {
            throw new \RuntimeException(sprintf('Could not write to file "%s".', $fileName));
        }
        $this->readFile($fileName);
        unlink($fileName);
        $this->fileName = NULL;
    }

    public function readArray($array) {
        $this->emconf = $array;
    }

    public function writeFile($fileName = NULL) {
        if(is_null($fileName)) {
            if(is_null($this->fileName)) {
                throw new \InvalidArgumentException('a fileName has to be given to save the file if you did not open an existing ext_emconf.php');
            } else {
                $fileName = $this->fileName;
            }
        }
        $success = file_put_contents($fileName, $this->toString());

        if(!$success) {
            throw new \RuntimeException(sprintf('Could not write to file "%s".', $fileName));
        }

        return TRUE;
    }

    public function toString() {
        $string = "<?php\n\n";
        $string .= $this->comment;
        $string .= "\n\$EM_CONF[\$_EXTKEY] = ";
        $string .= var_export($this->emconf, TRUE);
        $string .= ";\n\n?>";
        $string = preg_replace('/^(  )/ms', "\t", $string);
        return $string;
    }

    public function __toString() {
        return $this->toString();
    }

    public function toArray() {
        return $this->emconf;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getTitle() {
        return array_key_exists('title', $this->emconf) ? (string)$this->emconf['title'] : NULL;
    }

    public function setTitle($title) {
        $this->emconf['title'] = (string)$title;
    }

    public function getDescription() {
        return array_key_exists('description', $this->emconf) ? (string)$this->emconf['description'] : NULL;
    }

    public function setDescription($description) {
        $this->emconf['description'] = (string)$description;
    }

    public function getCategory() {
        return array_key_exists('category', $this->emconf) ? (string)$this->emconf['category'] : NULL;
    }

    public function setCategory($category = NULL) {
        $this->emconf['category'] = (string)$category;
    }

    public function isShy() {
        return array_key_exists('shy', $this->emconf) ? (bool)$this->emconf['shy'] : NULL;
    }

    public function setShy($shy) {
        $this->emconf['shy'] = $shy ? 1 : 0;
    }

//    public function getDependencies() {
//
//    }
//
//    public function setDependencies($dependencies) {
//
//    }
//
//    public function addDependency($extensionKey, $versionFrom = NULL, $versionTo = NULL) {
//
//    }
//
//    public function removeDependency($extensionKey) {
//
//    }
//
//    public function getConflicts() {
//
//    }
//
//    public function setConflicts($conflicts) {
//
//    }
//
//    public function addConflict($extensionKey, $versionFrom = NULL, $versionTo = NULL) {
//
//    }
//
//    public function removeConflict($extensionKey) {
//
//    }
//
//    public function getSuggestions() {
//
//    }
//
//    public function setSuggestion($dependencies) {
//
//    }
//
//    public function addSuggestion($extensionKey, $versionFrom = NULL, $versionTo = NULL) {
//
//    }
//
//    public function removeSuggestion($extensionKey) {
//
//    }

    public function getPriority() {
        return array_key_exists('priority', $this->emconf) ? (string)$this->emconf['priority'] : NULL;
    }

    public function setPriority($priority = NULL) {
        $this->emconf['priority'] = (string)$priority;
    }

    public function getState() {
        return array_key_exists('state', $this->emconf) ? (string)$this->emconf['state'] : NULL;
    }

    public function setState($state = NULL) {
        $this->emconf['state'] = (string)$state;
    }

//    public function getUploadFolder() {
//
//    }
//
//    public function setUploadFolder($uploadFolder) {
//
//    }
//
//    public function getCreatedDirectories() {
//
//    }
//
//    public function setCreatedDirectories($directories = array()) {
//
//    }
//
//    public function addCreatedDirectory($directoryName) {
//
//    }
//
//    public function removeCreatedDirectory($directoryName) {
//
//    }

    public function hasClearCacheOnLoad() {
        return array_key_exists('clearcacheonload', $this->emconf) ? (bool)$this->emconf['clearcacheonload'] : NULL;
    }

    public function setClearCacheOnLoad($clearCacheOnLoad = FALSE) {
        $this->emconf['clearcacheonload'] = $clearCacheOnLoad ? 1 : 0;
    }

    public function getAuthor() {
        return array_key_exists('author', $this->emconf) ? (string)$this->emconf['author'] : NULL;
    }

    public function setAuthor($author = NULL) {
        $this->emconf['author'] = (string)$author;
    }

    public function getAuthorEmail() {
        return array_key_exists('author_email', $this->emconf) ? (string)$this->emconf['author_email'] : NULL;
    }

    public function setAuthorEmail($email = NULL) {
        $this->emconf['author_email'] = (string)$email;
    }

    public function getAuthorCompany() {
        return array_key_exists('author_company', $this->emconf) ? (string)$this->emconf['author_company'] : NULL;
    }

    public function setAuthorCompany($company) {
        $this->emconf['author_company'] = (string)$company;
    }

    public function getVersion() {
        return array_key_exists('version', $this->emconf) ? (string)$this->emconf['version'] : NULL;
    }

    public function setVersion($version) {
        $this->emconf['version'] = (string)$version;
    }


}