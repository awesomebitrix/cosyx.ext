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
class JQuery_Widget extends CSX_Widget {
	protected function init() {
		parent::init();

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.js');
	}
}