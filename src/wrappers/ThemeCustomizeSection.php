<?php

namespace tad\wrappers;


use tad\utils\Str as Str;
use tad\adapters\Functions;
use tad\interfaces\FunctionsAdapter;

class ThemeCustomizeSection
{
    protected $functions = null;
    protected $sectionTitle;
    protected $description;
    protected $domain;
    protected $sectionId;
    protected $priority;
    protected $settings = array();
    protected $customControls = array();
    protected $classControlTypes = array('color', 'image', 'upload', 'background-image', 'header-image');
    protected $_includesPath = '';
    
    public function __construct($sectionTitle, $sectionId = null, $description = null, $domain = 'default', \tad\interfaces\FunctionsAdapter $functions = null)
    {
        $this->_includesPath = implode(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), 'src'));
        if (is_null($functions)) {
            $functions = new \tad\adapters\Functions();
        }
        $this->functions = $functions;
        $this->domain = $domain;
        $this->sectionTitle = $sectionTitle;
        if (is_null($sectionId)) {
            $sectionId = Str::hyphen($sectionTitle);
        }
        $this->sectionId = $sectionId;
        $this->description = $description;
        $this->priority = $this->getPriority();
        
        // load custom controls
        $this->loadControlsFrom('customizer controls');
        
        // hook into the 'customize_register' action
        $this->functions->add_action('customize_register', array($this, 'register'));
    }
    protected function getPriority()
    {
        return 35;
    }
    protected function loadControlsFrom($frags)
    {
        if (!is_string($frags) or $frags == '') {
            throw new \BadMethodCallException("Provide path components in a space separated list", 1);
        }
        $frags = explode(' ', trim($frags));
        $pathfrags = array($this->_includesPath);
        foreach ($frags as $frag) {
            array_push($pathfrags, $frag);
        }
        $controlsFolder = implode(DIRECTORY_SEPARATOR, $pathfrags);
        
        // fetch all *Control.php in the controls folder
        foreach (glob($controlsFolder . '/*CustomControl.php') as $fileName) {
            
            // MultiImageControl.php will have a slug of 'multi-image'
            $slug = Str::hyphen(preg_replace('/CustomControl/', '', basename($fileName, '.php')));
            
            // store the controls that come with the library
            // again presumin PSR-o compliancy the class name will be the same
            // as the filename
            $this->customControls[$slug] = implode('\\', $frags) . '\\' . basename($fileName, '.php');
        }
    }
    public function addSetting($id, $label = '', $default = '', $controlType = 'text', $arguments = null)
    {
        if (!is_string($id)) {
            throw new \BadMethodCallException("Id must be a string", 1);
        }
        if (!is_null($arguments) and !is_array($arguments)) {
            throw new \BadMethodCallException("Arguments must be an array", 2);
        }
        if (!is_string($label)) {
            throw new \BadMethodCallException("Label must be a string", 3);
        }
        if (!is_string($controlType)) {
            throw new \BadMethodCallException("Control type must be a string", 4);
        }
        $this->settings[$id] = array('label' => $label, 'default' => $default, 'controlType' => $controlType,);
        if (is_array($arguments)) {
            foreach ($arguments as $key => $value) {
                $this->settings[$id][$key] = $value;
            }
        }
    }
    public function register($wp_customize)
    {
        
        // set the arguments for the section
        $arguments = array('title' => $this->sectionTitle, 'priority' => $this->priority, 'capability' => 'edit_theme_options');
        if (!is_null($this->description)) {
            $arguments['description'] = $this->functions->__($this->description, $this->domain);
        }
        
        // do not add empty sections
        if (!count($this->settings)) {
            return;
        }
        
        // add this section
        $wp_customize->add_section($this->sectionId, $arguments);
        
        // add each setting
        foreach ($this->settings as $id => $arguments) {
            
            // some defaults are used here
            $settingArguments = array('default' => $arguments['default'], 'type' => 'option', 'capability' => 'edit_theme_options');
            
            // will add setting for 'some plugin' in
            // 'some_plugin[some_option]'
            $settingFullId = $this->sectionId . '[' . $id . ']';
            $wp_customize->add_setting($settingFullId, $settingArguments);
            
            // add the control with 'some_plugin_some_option' id
            $type = $arguments['controlType'];
            $label = $arguments['label'];
            $controlArguments = array('label' => $this->functions->__($label, $this->domain), 'section' => $this->sectionId, 'settings' => $settingFullId, 'type' => $type);
            if (isset($arguments['choices'])) {
                $controlArguments['choices'] = $arguments['choices'];
            }
            if (is_array($this->customControls) and array_key_exists($type, $this->customControls)) {
                
                // 'type' will be the slug the custom control is memorized with
                // an autoloader is supposed to be in place
                // or an exception will happily be thrown
                $controlClass = $this->customControls[$type];
                $wp_customize->add_control(new $controlClass($wp_customize, $id, $controlArguments));
            } elseif (in_array($type, $this->classControlTypes)) {
                $className = '\WP_Customize_' . Str::ucfirstUnderscore($type) . '_Control';
                $wp_customize->add_control(new $className($wp_customize, $id, $controlArguments));
            } else {
                $wp_customize->add_control($this->sectionId . '_' . $id, $controlArguments);
            }
        }
    }
    public function containsSetting($settingId)
    {
        if (in_array($settingId, array_keys($this->settings))) {
            return true;
        }
        return false;
    }
    public function containsSettingWith($settingId, $key, $value)
    {
        if (!is_string($key)) {
            throw new \BadMethodCallException("Key must be a string", 1);
        }
        if (!$this->containsSetting($settingId)) {
            return false;
        }
        if ($this->settings[$settingId][$key] != $value) {
            return false;
        }
        return true;
    }
    public function addCustomControl($slug, $className)
    {
        if (!is_string($slug)) {
            throw new \BadMethodCallException("Custom control slug must be a string", 1);
        }
        if (!is_string($className)) {
            throw new \BadMethodCallException("Fully qualified class name must be a string", 2);
        }
        $this->customControls[$slug] = $className;
    }
    public function __get($key)
    {
        return $this->$key;
    }
}
