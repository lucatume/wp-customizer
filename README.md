# WordPress customizer add-ons and wrappers

A set of custom controls by [paulund](https://github.com/paulund), a multi-image control and a theme customizer section wrapper.

## Theme customize section
Wraps methods and hooking needed to add sections and fields to the theme customize section to allow code like

    // add the section
    $section = new tad_ThemeCustomizeSection('Announcement', 'acme_announcement', 'The home page announcement.', 'acme');
    
    // add a checkbox to toggle showing the announcement on the home page
    $section->addSetting('announcement_show', 'Show the announcement on the homeapage.', true, 'checkbox');
    
    // add a text field for the title
    $section->addSetting('announcement_title', 'Title', 'Hello', 'textarea');
    
    // add a date picker for the date
    $section->addSetting('announcement_date', 'Date', '', 'date-picker');
    
    // add a file upload field for the flyer
    $section->addSetting('announcement_flyer', 'Flyer', '', 'upload');

values will be saved in the <code>acme_announcement</code> option in the database in an array format option.

## Customizer controls
I've added [paulund's](https://github.com/paulund/wordpress-theme-customizer-custom-controls) custom controls to the package and mine(s). As seen above those controls can be added to a section using their hyphen-separated slug. See the code for controls.  

    // add the Multi Image control
    $section->addSetting('some_images', 'Some images for fun', '', 'multi-image');
