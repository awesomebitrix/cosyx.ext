<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *  JQuery bxSlider widget
 *  @package cosyx.ext
 */
class JQuery_BxSlider_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');

        $this->application->AddHeadScript($this->getPubUrl() . '/js/jquery.bxslider.min.js');
    }
}