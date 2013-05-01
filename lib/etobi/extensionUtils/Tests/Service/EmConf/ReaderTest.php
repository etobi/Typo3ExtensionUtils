<?php

namespace etobi\extensionUtils\Tests\Service\EmConf;

use etobi\extensionUtils\Service\EmConf;

class ReaderWriterTest extends \PHPUnit_Framework_TestCase {

    protected $tempFiles = array();

    public function tearDown() {
        foreach($this->tempFiles as $tempFile) {
            unlink($tempFile);
        }
    }

    protected function getTempfileName($ext = NULL) {
        $fileName = tempnam(sys_get_temp_dir(), 'test');
        if($ext) {
            $fileName .= '.' . $ext;
        }
        $this->tempFiles[] = $fileName;
        return $fileName;
    }


    public function testFileReader() {
        $emconf = new EmConf();
        $emconf->readFile(__DIR__ . DIRECTORY_SEPARATOR . 'test_ext_emconf.php');

        $this->assertSame(
            'Extension Builder',
            $emconf->getTitle(),
            'configuration array read'
        );

        $this->assertContains(
            'config file for ext',
            $emconf->getComment(),
            'comment read'
        );
    }

    public function testStringReader() {
        $emconf = new EmConf();
        $emconf->readString(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'test_ext_emconf.php'));

        $this->assertSame(
            'Extension Builder',
            $emconf->getTitle(),
            'configuration array read'
        );

        $this->assertContains(
            'config file for ext',
            $emconf->getComment(),
            'comment read'
        );
    }

    public function testToString() {
        $emconf = new EmConf();
        $emconf->readFile(__DIR__ . DIRECTORY_SEPARATOR . 'test_ext_emconf.php');

        $string = $emconf->toString();

        $this->assertStringStartsWith('<?php', $string, 'string starts with an opening PHP tag');
        $this->assertStringEndsWith('?>', $string, 'string ends with a closing PHP tag');
        $this->assertContains('/*', $string, 'contains a comment');
        $this->assertContains('$EM_CONF[$_EXTKEY] = array (', $string, 'contains a configuration array');
        $this->assertContains("'title' => 'Extension Builder',", $string, 'contains configuration');
        $this->assertNotRegExp('/^  /ms', $string, 'indentation is not done with spaces');
    }

    public function testWriteFile() {
        $fileName = $this->getTempfileName('php');
        copy(__DIR__ . DIRECTORY_SEPARATOR . 'test_ext_emconf.php', $fileName);

        $originalContent = file($fileName);

        $emconf = new EmConf();
        $emconf->readFile($fileName);
        // set content to something to see if writeFile actually writes something
        file_put_contents($fileName, 'test failed');
        $emconf->writeFile();

        $this->assertNotContains('test failed', file_get_contents($fileName), 'writeFile() wrote a file');
        $emconf2 = new EmConf();
        $emconf2->readFile($fileName);
        $this->assertSame($emconf->getTitle(), $emconf2->getTitle(), 'EmConf can read array of its created files');
        $this->assertSame($emconf->getComment(), $emconf2->getComment(), 'EmConf can read comment of its created files');

        $modifiedContent = file($fileName);

        $this->assertSame(
            $originalContent,
            $modifiedContent,
            'writeFile does not change the content of a well formed ext_emconf.php if nothing was changed'
            // necessary for version control
        );

    }


}