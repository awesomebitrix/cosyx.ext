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
class JQuery_JQPlot_Widget extends CSX_SingletonWidget {
    protected function init() {
        parent::init();

        CSX_Widget::includeWidget('JQuery');

        $this->application->AddHeadScript($this->getPubUrl() . '/excanvas.min.js');
        $this->application->AddHeadScript($this->getPubUrl() . '/jquery.jqplot.min.js');
        $this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.jqplot.min.css');

        if ($this->params->has('plugins')) {
            foreach ($this->params->get('plugins') as $plugin) {
                $this->application->AddHeadScript($this->getPubUrl() . '/plugins/jqplot.' . $plugin . '.js');
            }
        }
    }
}