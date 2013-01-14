<?php
class JQuery_MaskedInput_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();

		CSX_Widget::includeWidget('JQuery');
		$this->application->AddHeadScript($this->getPubUrl() . '/jquery.maskedinput-1.3.min.js');
	}
}