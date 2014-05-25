<?php

use \tad\wrappers\ThemeSupport;

class ThemeSupportTest extends \PHPUnit_Framework_TestCase
{
    protected $sut;
    public function setUp()
    {
        $this->sut = new ThemeSupport();
    }
    public function testWillInitFunctionsAdapterIfNonePassed()
    {
        $this->assertInstanceOf('\tad\adapters\Functions', $this->sut->getFunctions());
    }
    public function testWillAddAndRemoveThemeSupport()
    {
        $this->sut->add('some-feature');
        $this->assertArrayHasKey('some-feature', $this->sut->getToAdd());
        $this->sut->remove('some-feature');
        $this->assertArrayNotHasKey('some-feature', $this->sut->getToAdd());
        $this->assertArrayHasKey('some-feature', $this->sut->getToRemove());
        $this->sut->add('some-feature');
        $this->assertArrayHasKey('some-feature', $this->sut->getToAdd());
        $this->assertArrayNotHasKey('some-feature', $this->sut->getToRemove());
        $this->sut->remove('some-feature');
        $this->assertArrayNotHasKey('some-feature', $this->sut->getToAdd());
        $this->assertArrayHasKey('some-feature', $this->sut->getToRemove());
        $this->sut->add('some-feature', array('with', 'options'));
        $this->assertArrayHasKey('some-feature', $this->sut->getToAdd());
        $this->assertArrayNotHasKey('some-feature', $this->sut->getToRemove());
    }
    public function testAddAndRemoveWillCallAddThemeSupport()
    {
        $mockFunctions = $this->getMock('\tad\interfaces\FunctionsAdapter', array('__call', 'add_theme_support'));
        $mockFunctions->expects($this->once())
            ->method('add_theme_support')->with('some-feature');
        $sut = new ThemeSupport($mockFunctions);
        $sut->add('some-feature');
        $sut->addAndRemove();
    }
    public function testAddAndRemoveWillCallRemoveThemeSupport()
    {
        $mockFunctions = $this->getMock('\tad\interfaces\FunctionsAdapter', array('__call', 'remove_theme_support'));
        $mockFunctions->expects($this->once())
            ->method('remove_theme_support')->with('some-feature');
        $sut = new ThemeSupport($mockFunctions);
        $sut->remove('some-feature');
        $sut->addAndRemove();
    }
    public function testAddingThemeSupportWithArgumentsWork()
    {
        $mockFunctions = $this->getMock('\tad\interfaces\FunctionsAdapter', array('__call', 'add_theme_support'));
        $mockFunctions->expects($this->once())
            ->method('add_theme_support')->with('some-feature', 'foo', 'baz', 'bar');
        $sut = new ThemeSupport($mockFunctions);
        $sut->add('some-feature', array('foo', 'baz', 'bar'));
        $r = $sut->getToAdd();
        $this->assertArrayHasKey('some-feature', $r);
        $this->assertContains('foo', $r['some-feature']);
        $this->assertContains('baz', $r['some-feature']);
        $this->assertContains('bar', $r['some-feature']);
        $sut->addAndRemove();
    }
    public function testAddAndRemoveWillCallAddThemeSupportWithArguments()
    {
        $mockFunctions = $this->getMock('\tad\interfaces\FunctionsAdapter', array('__call', 'add_theme_support'));
        $mockFunctions->expects($this->once())
            ->method('add_theme_support')->with('some-feature', 'foo', 'baz', 'bar');
        $sut = new ThemeSupport($mockFunctions);
        $sut->add('some-feature', array('foo', 'baz', 'bar'));
        $sut->addAndRemove();
    }
    public function testAddedFeaturesCanBeAccessedAsProperties()
    {
        $this->sut->add('some-feature', array('foo'));
        $r = $this->sut->someFeature;
        $this->assertEquals(array('foo'), $r);
    }
}

