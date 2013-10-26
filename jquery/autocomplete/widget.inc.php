<?php
class JQuery_Autocomplete_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');
		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.autocomplete.min.js');
		$this->application->AddHeadStylesheet($this->getPubUrl() . '/jquery.autocomplete.css');
	}
}