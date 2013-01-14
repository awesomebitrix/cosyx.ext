<?php
class JQuery_Boxy_Ex_Widget extends CSX_UI_SingletonWidget {
	protected function init() {
		parent::init();
		
		CSX_Widget::includeWidget('JQuery');
		CSX_Widget::includeWidget('JQuery::Boxy');
		$this->application->AddHeadScript($this->getPubUrl() . '/boxyex.js');
	}
}