<?php
/*
 * 自定义用户的基类 
 * 
 */

class CommonAction extends Action {
    
    public function __construct() {
        /* 当没有uid即 用户没有登陆的时候 自动注册一个用户///
         * 当用户退出/或者在注册页面的时候不是继承Common、即UserAction是直接继承Action的
         * 用户可以在Login或者Register注册自己的账号 或者 直接修改随机个人帐号
         * 随即账号密码为123456
         */

        R("Public/session_start_by_user");
        session_start();
        
        
        if (session("uid") == "" || session("uid") == null || session("name") == "" || session("name") == null) {
            load("extend");     //引入扩展函数库
            do {
                //当用外部浏览器登陆的时候就用这个 微信暂时也用这个
                $userName = "dc".rand_string(8, 1);
                $userPassword = sha1(md5("123456"));
                $flag = D("User")->getUserRegister($userName, $userPassword);
            } while (!$flag);
            session_set_cookie_params(3600 * 24 * 365, "/");
            session('[regenerate]');
            session("uid", $flag['uid']);
            session("name", $userName);
            session("password", $userPassword);
        }
        
        //如果没有选择地点则跳转选择地点
        if (!session("areaId")) {
            $this->redirect("/area");
        }        
    }
}
?>