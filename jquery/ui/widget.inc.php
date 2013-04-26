<?php
class JQuery_UI_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');

		$this->application->AddHeadScript($this->getPubUrl() . '/js/jquery-ui-1.8.11.custom.min.js');

		if (!defined('LANG_CHARSET') || LANG_CHARSET!='windows-1251') {
			$this->application->AddHeadScript($this->getPubUrl() . '/js/i18n/jquery.ui.datepicker-ru.js');
		}
		else {
			$this->application->AddHeadScript($this->getPubUrl() . '/js/i18n/jquery.ui.datepicker-ru1251.js');
		}


		$theme = $this->params->get('theme');
		if (!$theme) $theme = 'redmond';
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/themes/' . $theme . '/jquery-ui-1.8.12.custom.css');
	}
}