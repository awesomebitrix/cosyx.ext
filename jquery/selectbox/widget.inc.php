<?php
class JQuery_Selectbox_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.selectBox.min.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.selectBox.css');
	}
}