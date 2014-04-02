<?php

class EmptyAction extends Action {
    public function __construct() {
        R("Public/session_start_by_user");
        session_start();
    }
    public function _empty() {
        $this->assign("title", "错误提示");
        $this->assign("message", "该页面不存在");
        $this->assign("jumpUrl", "/");
        
        R("Public/recordUserVisited");          //记录
        $this->error();
    }
}