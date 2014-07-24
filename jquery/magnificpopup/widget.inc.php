<?php
class JQuery_MagnificPopup_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');
        $this->application->AddHeadScript($this->getPubUrl() . '/jquery.magnific-popup.min.js');
        $this->application->AddHeadStylesheet($this->getPubUrl() . '/magnific-popup.css');
    }
}