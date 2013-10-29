<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *	JQuery LiSlider widget
 *	@package cosyx.ext
 */
class JQuery_LiSlider_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/js/jquery.easing.1.3.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/js/li-slider-animations-1.1.min.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/js/li-slider-1.1.js');

		$theme = $this->getParam('theme', 'Trend');

		$this->application->AddHeadStylesheet($this->getPubUrl() . '/skins/' . $theme . '/skin.css');
	}
}