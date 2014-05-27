<?php
class JQuery_Migrate_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');

        $this->application->AddHeadScript($this->getPubUrl() . '/jquery-migrate-1.2.1.min.js');
    }
}