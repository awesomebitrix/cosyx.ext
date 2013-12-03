<?php
class JQuery_Fancybox_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');

        $this->application->AddHeadScript($this->getPubUrl() . '/jquery.fancybox.js?v=2.1.5');
        $this->application->AddHeadScript($this->getPubUrl() . '/helpers/jquery.fancybox-buttons.js?v=1.0.5');
        $this->application->AddHeadScript($this->getPubUrl() . '/helpers/jquery.fancybox-thumbs.js?v=1.0.7');
        $this->application->AddHeadScript($this->getPubUrl() . '/helpers/jquery.fancybox-media.js?v=1.0.6');

        $this->application->AddHeadStylesheet($this->getPubUrl() . '/helpers/jquery.fancybox-buttons.css?v=1.0.5');
        $this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.fancybox.css?v=2.1.5');
        $this->application->AddHeadStylesheet($this->getPubUrl() . '/helpers/jquery.fancybox-thumbs.css?v=1.0.7');
    }
}