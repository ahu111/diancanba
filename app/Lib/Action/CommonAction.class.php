<?php
/*
 * 自定义用户的基类 
 * 
 */

class CommonAction extends Action {
    
    function isMobile() {  
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备  
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {  
            return true;  
        }  
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息  
        if (isset($_SERVER['HTTP_VIA'])) {  
            //找不到为flase,否则为true  
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;  
        }  
        //判断手机发送的客户端标志,兼容性有待提高  
        if (isset($_SERVER['HTTP_USER_AGENT'])) {  
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');  
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字  
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {  
                return true;  
            }  
        }  
        //协议法，因为有可能不准确，放到最后判断  
        if (isset($_SERVER['HTTP_ACCEPT'])) {  
            // 如果只支持wml并且不支持html那一定是移动设备  
            // 如果支持wml和html但是wml在html之前则是移动设备  
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {  
                return true;  
            }  
        }  
        return false;  
    } 
    
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
