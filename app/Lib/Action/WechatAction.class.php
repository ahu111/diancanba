<?php

class WechatAction extends Action {
	public function __construct() {
		vendor("wechat");
		$wechat = new wechat();
		$wechat->valid();
		$wechat->createMenu();
	}	
}

?>