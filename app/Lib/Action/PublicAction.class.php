<?php



class PublicAction extends Action {
	//记录用户访问信息   请求的页面/访问的事件/访问的设备
    public function recordUserVisited() {
        $REQUEST_URI = $_SERVER["REQUEST_URI"];     //请求的地址
        $IP = $_SERVER['REMOTE_ADDR'];          //IP
        $HTTP_REFERER = $_SERVER['HTTP_REFERER'] == null ? "" : $_SERVER['HTTP_REFERER'];       //前一地址
        $userId = session("uid") == null ? "" : session("uid");       //用户id
        $date = date("Y-m-d H:i:s");        //时间
        
        M("UserVisitedLogs")->add(array("IP" => $IP, "HTTP_REFERER" => $HTTP_REFERER, "user_id" => $userId, "REQUEST_URI" => $REQUEST_URI,
            "date" => $date));        
    }
    
   
    public function session_start_by_user() {
        session("name", "__wx__");
        session_set_cookie_params(3600 * 24 * 365, "/");
        session('expire', 3600 * 24 * 365);     
    }

    /**
     * 首先页面跳到这一页然后利用javascript读取localStorage再来进行判断
     */
    public function tempage() {
        $this->display();
    }

}