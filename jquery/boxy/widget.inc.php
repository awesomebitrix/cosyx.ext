<?php
class JQuery_Boxy_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/js/jquery.boxy.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/css/boxy.css');

		if (LANG_CHARSET=='windows-1251') {
			$this->application->AddHeadScript($this->getPubUrl() . '/js/jquery.boxy.1251.js');
		}
	}
}