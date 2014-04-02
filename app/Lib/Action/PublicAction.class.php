<?php

define("TOKEN", "diancanba");

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
    
    //微信开发者验证
    public  function weixin() {
        $this->valid();
    }
    /*
     * 微信公众号开发接口
     * 
     */
    public function valid(){
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg() {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
    private function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

            if( $tmpStr == $signature ){
                    return true;
            }else{
                    return false;
        }
    }
}