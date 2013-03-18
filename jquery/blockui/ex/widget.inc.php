<?php
class JQuery_BlockUI_Ex_Widget extends CSX_SingletonWidget {
	protected function init() {
		parent::init();
		
		CSX_Widget::includeWidget('JQuery');
		CSX_Widget::includeWidget('JQuery::BlockUI');
		$this->application->AddHeadScript($this->getPubUrl() . '/ex.js');
	}
}