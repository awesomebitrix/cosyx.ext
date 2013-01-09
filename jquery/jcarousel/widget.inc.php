<?php
class JQuery_JCarousel_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		if (!$this->isInited()) {
			$this->application->AddHeadScript($this->getPubUrl() . '/lib/jquery.jcarousel.min.js');
			$this->application->AddHeadString('<link href="' . $this->getPubUrl() . '/skins/tango/skin.css" type="text/css" rel="stylesheet" />', true);
		}
	}
}