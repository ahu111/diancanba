<?php

class ActivityAction extends Action {
    
    public function __construct() {
        R("Public/session_start_by_user");
        session_start();
    }
    
    public function share_get_credit(){
        
        $suid = intval($this->_param(2)) / 2332;        //2332算是一个加密的数字
        
        
        //存在该用户  且该用户不是自己
        if (D("User")->getUserInfoByUserId($suid) && session("uid") != $suid) {
            //如果用户拿过积分就不再拿了
            if (!M("activity_credit_log")->where("uid = $suid")->find()) {
                $credit = intval(rand(2, 5));            //产生积分
                M("activity_credit_log")->add(array("uid" => $suid, "credit" => $credit, "type" => "share_get_credit", "date" => date("Y-m-d H:i:s")));
                M("User")->where("uid = $suid")->setInc("user_credit", $credit);
            }
        }
        
        $this->display();
    }
}

