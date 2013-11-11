<?php
class JQuery_Selectbox_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/js/jquery.selectbox-0.2.min.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/css/jquery.selectbox.css');
	}
}