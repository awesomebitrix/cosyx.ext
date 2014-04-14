<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *  @package cosyx.ext
 */
class JQuery_JCarousel_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');

        $this->application->AddHeadScript($this->getPubUrl() . '/lib/jquery.jcarousel.min.js');
    }
}