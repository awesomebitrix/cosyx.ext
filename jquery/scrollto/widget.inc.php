<?php
class JQuery_ScrollTo_Widget extends CSX_Widget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.scrollTo-1.4.2-min.js');
	}
}