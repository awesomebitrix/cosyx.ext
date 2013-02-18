<?php

/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx.ext
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *	JQuery widget
 *	@package cosyx.ext
 */
class JQuery_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery-1.8.3.min.js');
	}
}