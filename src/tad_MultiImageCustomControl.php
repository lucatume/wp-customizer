<?php

if (!class_exists('\WP_Customize_Control')) {
    return null;
}

class tad_MultiImageCustomControl extends WP_Customize_Control
{
    public $type = 'multi-image';

    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
    }

    public function enqueue()
    {
        wp_enqueue_media();
        $jsPath = '/js/multi-image.js';
        wp_enqueue_script('multi-image-control', tad_Script::suffix($jsPath), array('jquery', 'jquery-ui-sortable'));
        $cssPath = '/css/multi-image.css';
        wp_enqueue_style('multi-image-control', tad_Script::suffix($cssPath));
    }
    public function render_content()
    {
        // get the set values if any
        $imageSrcs = explode(',', $this->value());
        if (!is_array($imageSrcs)) {
            $imageSrcs = array();
        }
        $this->theTitle();
        $this->theButtons();
        $this->theUploadedImages($imageSrcs);
    }
    protected function theTitle()
    {
        ?>
        <label>
            <span class="customize-control-title">
                <?php echo esc_html($this->label); ?>
            </span>
        </label>
        <?php
    }
    protected function getImages()
    {
        $options = $this->value();
        if (!isset($options['image_sources'])) {
            return '';
        }
        return $options['image_sources'];
    }
    public function theButtons()
    {
        ?>
        <div>
            <input type="hidden" value="<?php echo $this->value(); ?>" <?php $this->link(); ?> class="multi-images-control-input"/>
            <a href="#" class="button-secondary multi-images-upload">
                <?php echo 'Upload'; ?>
            </a>
            <a href="#" class="button-secondary multi-images-remove">
               <?php echo 'Remove all images'; ?>
           </a>
       </div>
       <?php
   }
   public function theUploadedImages($srcs = array())
   {
    ?>
    <div class="customize-control-content">
        <ul class="thumbnails">
            <?php if (is_array($srcs)): ?>
                <?php foreach ($srcs as $src): ?>
                    <?php if ($src != ''): ?>
                        <li class="thumbnail" style="background-image: url(<?php echo $src; ?>);" data-src="<?php echo $src; ?>" >
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?> 
            <?php endif; ?>
        </ul>
    </div>
    <?php
}
}
