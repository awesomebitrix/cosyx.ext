<?php
class JQuery_Validation_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.validate.js');
		$this->application->AddHeadScript($this->getPubUrl() . '/additional-methods.js');
	
		if (!defined('LANG_CHARSET') || LANG_CHARSET!='windows-1251') {
			$this->application->AddHeadScript($this->getPubUrl() . '/jquery.validate.wrap.js');
		}
		else {
			$this->application->AddHeadScript($this->getPubUrl() . '/jquery.validate.wrap1251.js');
		}

		$this->application->AddHeadStylesheet($this->getPubUrl() . '/style.css');
	}
}