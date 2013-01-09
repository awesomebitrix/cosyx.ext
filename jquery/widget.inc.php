<?php
/**
 *	PrimalSite CMS Project
 *
 *	@package ui
 *	@version $Id$
 */

/**
 *	JQuery widget
 *
 *	@package ui
 */
class JQuery_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		if (!$this->isInited()) {
			$this->application->AddHeadScript($this->getPubUrl() . '/jquery-1.8.3.min.js');
		}
	}
}