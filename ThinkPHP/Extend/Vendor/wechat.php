<?php


define("TOKEN", "diancanba");
define("AppId", "wx19b280ce989aab33");
define("AppSecret", "1b2a84f0ddde671c76618b1b2df6ad24");

/*微信接口
*
*/
class wechat {

    //验证账号有效性
	public function valid(){
		$echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
	}

	/* wechat 验证开发者账号 */
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
        } else {
            return false;
        }
    }

    /*获取AccessToken*/
    private function getAccessToken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".AppId."&secret=".AppSecret;
        $data = $this->getCurl($url);//通过自定义函数getCurl得到https的内容
        $resultArr = json_decode($data, true);//转为数组
        return $resultArr["access_token"];//获取access_token
    }
    /*微信创建菜单*\

    */
    public function createMenu(){
        $accessToken = $this->getAccessToken();
        $menuJsonArray = ' {
             "button":[
             {  
                  "type":"view",
                  "name":"前往点餐",
                  "url":"http://w.xiaoplus.com/"
              },
              {
                   "type":"click",
                   "name":"最新活动",
                   "key":"V1001_TODAY_SINGER"
              },
              {
                   "name":"个人中心",
                   "sub_button":[
                   {    
                       "type":"view",
                       "name":"我的订单",
                       "url":"http://w.xiaoplus.com/order/"
                    },
                    {
                       "type":"view",
                       "name":"个人资料",
                       "url":"http://w.xiaoplus.com/user/"
                    }]
               }]
         }';

         $menuPostUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;//POST的url
         $menu = $this->dataPost($menuJsonArray, $menuPostUrl);//将菜单结构体POST给微信服务器
    }


    /*获取微信关注用户的信息*/
    public function getUserInfo() {

    }

    //get https的内容
    private function getCurl($url){
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL,$url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
         $result =  curl_exec($ch);
         curl_close ($ch);
         return $result;
    }

    //POST方式提交数据
    private function dataPost($post_string, $url) {
         $context = array ('http' => array ('method' => "POST", 'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) \r\n Accept: */*", 'content' => $post_string ) );
         $stream_context = stream_context_create ( $context );
         $data = file_get_contents ( $url, FALSE, $stream_context );
         return $data;
    }


}


?>