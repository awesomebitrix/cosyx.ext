<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *	@package cosyx.ext
 */
class JQuery_Lightbox_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.lightbox.min.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.lightbox.css');
	}
}