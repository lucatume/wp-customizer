<?php
use tad\utils\__ as __;

class __Test extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testShouldBeDefined()
    {
        $this->assertTrue(class_exists('\tad\utils\__'));
    }
    public function testShouldExtendUnderscorePhp()
    {
        $this->assertEquals('brianhaveri\__', get_parent_class('\tad\utils\__'));
    }
    public function testShouldDefineAllUnderscorePhpMethods()
    {
        $originalClassMethods = get_class_methods('brianhaveri\__');
        $newClassMethods = get_class_methods('tad\utils\__');
        foreach ($originalClassMethods as $key) {
            $this->assertContains($key, $newClassMethods);
        }
    }
    public function testShouldAllowCallingAllStrStaticMethods()
    {
        $this->assertEquals('someName', __::camelBack('Some Name'));
        $this->assertEquals('SomeName', __::camelCase('Some Name'));
        $this->assertEquals('some_name', __::underscore('Some Name'));
        $this->assertEquals('some-name', __::hyphen('Some Name'));
        $this->assertEquals('Some_Name', __::ucfirstUnderscore('Some Name'));
        $this->assertEquals('Some/name', __::toPath('Some name'));
    }
    public function testShouldAllowCallingAllArrStaticMethods()
    {
        $this->assertTrue(__::isAssoc(array('some'=>'stuff')));
    }
    public function testShouldAllowCallingAllScriptStaticMethods()
    {
        $this->assertEquals('some/path/to/file.min.js', __::suffix('some/path/to/file.js', true));
    }
}