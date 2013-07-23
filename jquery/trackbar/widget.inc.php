<?php
class JQuery_Trackbar_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.trackbar.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.trackbar.css');
	}
}