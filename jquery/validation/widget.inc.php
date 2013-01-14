<?php
class JQuery_Validation_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.validate.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/additional-methods.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.validate.wrap.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/style.css');
	}
}