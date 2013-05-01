<?php

namespace etobi\extensionUtils\Tests\Service\EmConf;

use etobi\extensionUtils\Service\EmConf;

class GetterSetterTest extends \PHPUnit_Framework_TestCase {

    protected $defaultData = array (
        'title' => 'Foobar 42',
        'description' => 'Lorem ipsum sid dolor',
        'category' => 'module',
        'shy' => 0,
        'version' => '1.2.3',
        'priority' => '',
        'state' => 'beta',
        'clearcacheonload' => 0,
        'author' => 'John Doe',
        'author_email' => 'doe@example.com',
        'author_company' => 'Foobar Corp.',
    );

    /**
     * @var EmConf
     */
    protected $emconf;

    public function setUp() {
        $this->emconf = new EmConf();
        $this->emconf->readArray($this->defaultData);
    }

    public function testTitle() {
        $this->assertSame(
            'Foobar 42',
            $this->emconf->getTitle(),
            'get title from given array'
        );

        $this->emconf->setTitle('Foobar 43');

        $this->assertSame(
            'Foobar 43',
            $this->emconf->getTitle(),
            'title set in setTitle is available in getTitle'
        );

        $this->assertArrayHasKey(
            'title',
            $this->emconf->toArray(),
            'title set in setTitle is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['title'],
            'Foobar 43',
            'title set in setTitle is available in array export #2'
        );
    }

    public function testDescription() {
        $this->assertSame(
            'Lorem ipsum sid dolor',
            $this->emconf->getDescription(),
            'get description from given array'
        );

        $this->emconf->setDescription('Lorem ipsum');

        $this->assertSame(
            'Lorem ipsum',
            $this->emconf->getDescription(),
            'description set in setDescription is available in getDescription'
        );

        $this->assertArrayHasKey(
            'description',
            $this->emconf->toArray(),
            'description set in setDescription is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['description'],
            'Lorem ipsum',
            'description set in setDescription is available in array export #2'
        );
    }

    public function testCategory() {
        $this->assertSame(
            'module',
            $this->emconf->getCategory(),
            'get description from given array'
        );

        $this->emconf->setCategory(EmConf::CATEGORY_EXAMPLE);

        $this->assertSame(
            EmConf::CATEGORY_EXAMPLE,
            $this->emconf->getCategory(),
            'category set in setCategory is available in getCategory'
        );

        $this->assertArrayHasKey(
            'category',
            $this->emconf->toArray(),
            'category set in setCategory is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['category'],
            EmConf::CATEGORY_EXAMPLE,
            'category set in setCategory is available in array export #2'
        );
    }

    public function testShy() {
        $this->assertSame(
            FALSE,
            $this->emconf->isShy(),
            'get shy from given array'
        );

        $this->emconf->setShy(TRUE);

        $this->assertSame(
            TRUE,
            $this->emconf->isShy(),
            'shy set in setShy is available in isShy'
        );

        $this->assertArrayHasKey(
            'shy',
            $this->emconf->toArray(),
            'shy set in setShy is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['shy'],
            1,
            'shy set in setShy is available in array export #2'
        );
    }

    public function testPriority() {
        $this->assertSame(
            EmConf::PRIORITY_DEFAULT,
            $this->emconf->getPriority(),
            'get priority from given array'
        );

        $this->emconf->setPriority(EmConf::PRIORITY_TOP);

        $this->assertSame(
            EmConf::PRIORITY_TOP,
            $this->emconf->getPriority(),
            'shy set in setPriority is available in getPriority'
        );

        $this->assertArrayHasKey(
            'priority',
            $this->emconf->toArray(),
            'priority set in setPriority is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['priority'],
            EmConf::PRIORITY_TOP,
            'priority set in setPriority is available in array export #2'
        );
    }

    public function testState() {
        $this->assertSame(
            EmConf::STATE_BETA,
            $this->emconf->getState(),
            'get state from given array'
        );

        $this->emconf->setState(EmConf::STATE_TEST);

        $this->assertSame(
            EmConf::STATE_TEST,
            $this->emconf->getState(),
            'shy set in setShy is available in isShy'
        );

        $this->assertArrayHasKey(
            'state',
            $this->emconf->toArray(),
            'state set in setState is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['state'],
            EmConf::STATE_TEST,
            'state set in setState is available in array export #2'
        );
    }

    public function testClearCacheOnLoad() {
        $this->assertSame(
            FALSE,
            $this->emconf->hasClearCacheOnLoad(),
            'get clearCacheOnLoad from given array'
        );

        $this->emconf->setClearCacheOnLoad(TRUE);

        $this->assertSame(
            TRUE,
            $this->emconf->hasClearCacheOnLoad(),
            'clearCacheOnLoad set in setClearCacheOnLoad is available in hasClearCacheOnLoad'
        );

        $this->assertArrayHasKey(
            'clearcacheonload',
            $this->emconf->toArray(),
            'clearCacheOnLoad set in setClearCacheOnLoad is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['clearcacheonload'],
            1,
            'clearCacheOnLoad set in setClearCacheOnLoad is available in array export #2'
        );
    }

    public function testAuthor() {
        $this->assertSame(
            'John Doe',
            $this->emconf->getAuthor(),
            'get author from given array'
        );

        $this->emconf->setAuthor('Jane Doe');

        $this->assertSame(
            'Jane Doe',
            $this->emconf->getAuthor(),
            'author set in setAuthor is available in getAuthor'
        );

        $this->assertArrayHasKey(
            'author',
            $this->emconf->toArray(),
            'author set in setAuthor is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['author'],
            'Jane Doe',
            'author set in setAuthor is available in array export #2'
        );
    }

    public function testAuthorEmail() {
        $this->assertSame(
            'doe@example.com',
            $this->emconf->getAuthorEmail(),
            'get author_email from given array'
        );

        $this->emconf->setAuthorEmail('john@example.com');

        $this->assertSame(
            'john@example.com',
            $this->emconf->getAuthorEmail(),
            'author_email set in setAuthorEmail is available in getAuthorEmail'
        );

        $this->assertArrayHasKey(
            'author_email',
            $this->emconf->toArray(),
            'author_email set in setAuthorEmail is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['author_email'],
            'john@example.com',
            'author_email set in setAuthorEmail is available in array export #2'
        );
    }

    public function testAuthorCompany() {
        $this->assertSame(
            'Foobar Corp.',
            $this->emconf->getAuthorCompany(),
            'get author_company from given array'
        );

        $this->emconf->setAuthorCompany('BazBar Ltd.');

        $this->assertSame(
            'BazBar Ltd.',
            $this->emconf->getAuthorCompany(),
            'author_company set in setAuthorCompany is available in getAuthorCompany'
        );

        $this->assertArrayHasKey(
            'author_company',
            $this->emconf->toArray(),
            'author_company set in setAuthorCompany is available in array export #1'
        );
        $this->assertSame(
            $this->emconf->toArray()['author_company'],
            'BazBar Ltd.',
            'author_company set in setAuthorCompany is available in array export #2'
        );
    }


}