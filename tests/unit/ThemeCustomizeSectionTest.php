<?php
use tad\wrappers\ThemeCustomizeSection;

class ThemeCustomizeSectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sut = new ThemeCustomizeSection('Some Section','some_section', 'Some section description', 'someDomain');
    }
    public function testAddSettingWillAddTheSetting()
    {
        $this->sut->addSetting('someSetting', 'Some setting', 'default value', 'checkbox');
        $this->assertTrue($this->sut->containsSetting('someSetting'));
    }
    public function testSettingArgumentsAreAppendedToTheSettingArray()
    {
        $arr = array('foo' => 'baz', 'bar' => 23);
        $this->sut->addSetting('someSetting', 'Some setting', 'default value', 'checkbox', $arr);
        $this->assertTrue($this->sut->containsSetting('someSetting', 'title', 'Some setting'));
        $this->assertTrue($this->sut->containsSettingWith('someSetting','default', 'default value'));
        $this->assertTrue($this->sut->containsSettingWith('someSetting','controlType', 'checkbox'));
        $this->assertTrue($this->sut->containsSettingWith('someSetting','foo', 'baz'));
        $this->assertTrue($this->sut->containsSettingWith('someSetting','bar', '23'));
    }

    public function testCallingRegisterWithNoSettingsAddedWillNotAddSection()
    {
        $f = $this->getMock('stdClass', array('add_section'));
        $f->expects($this->never())
        ->method('add_section');
        $this->sut->register($f);
    }
    public function testAddSectionIsCalledOnceWithProperIdAndArguments()
    {
        // add 2 settings
        $this->sut->addSetting('first_setting', 'First Setting', false, 'checkbox');
        $f = $this->getMock('stdClass', array('add_section', 'add_setting', 'add_control'));
        $f->expects($this->once())
        ->method('add_section')
        ->with('some_section');
        $f->expects($this->once())
        ->method('add_setting')
        ->with('some_section[first_setting]',
            array(
                'default' => false,
                'type' => 'option',
                'capability' => 'edit_theme_options'
                )
            );
        $f->expects($this->once())
        ->method('add_control')
        ->with('some_section_first_setting',
            array(
                // null due to missing mock of the functions adapter
                'label' => null,
                'section' => 'some_section',
                'settings' => 'some_section[first_setting]',
                'type' => 'checkbox'
                )
            );
        $this->sut->register($f);
    }
}
