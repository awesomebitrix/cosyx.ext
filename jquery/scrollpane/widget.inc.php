<?php
class JQuery_ScrollTo_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.mousewheel.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.jscrollpane.min.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.jscrollpane.css');
	}
}