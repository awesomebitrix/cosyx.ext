<?php

/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *  JQuery widget
 *  @package cosyx.ext
 */
class JQuery_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        if ($this->params->has('v')) {
            $this->application->AddHeadScript('http://code.jquery.com/jquery-' . $this->params->get('v') . '.min.js');
        }
        else {
            $this->application->AddHeadScript($this->getPubUrl() . '/jquery-1.11.0.min.js');
        }
    }
}