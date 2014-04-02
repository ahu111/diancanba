<?php


//新郎IP接口
class SinaIpLocation {
	
	private $data = "";							//返回的值
	private $startIp = "";						//ip起始字段
	private $endIp = "";						//ip结束字段
	private $ip = "";						//IP地址
	private $country = "";					//国家
	private $province = "";					//地区省市
	private $city = "";						//城市
	private $isp = "";						//网络服务提供商
	private $domain = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js";			//新浪API地址


	public function __construct() {

		//新浪API地址
		$curl = curl_init();
		//获得用户ip
		$this->ip = $this->get_onlineip();

		curl_setopt($curl, CURLOPT_URL, $this->domain."&ip=".$this->ip);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$this->data = $this->getStringArray(curl_exec($curl));					//返回的数据
		curl_close($curl);
	}

	//由于返回的是一个字符串、所以截取等号之后的字符
	private function getStringArray($string) {

		$stringTmp = explode("=", $string);	//取得等号后边的
		$string = $stringTmp[1];
		$string = preg_replace("/({|}|;)/", "", $string);			//剥离 { } ;
		$KeyValue = explode(",", $string);						//根据， 分离
		$dataArray = array();

		//剥离键值对
		foreach ($KeyValue as $var) {
			$tmpArray = explode(":", $var);
			$temp =  json_decode('['.$tmpArray[1].']');
			$tmpArray[1] = $temp[0];			//unicode转化为utf-8
			$temp = explode("\"", $tmpArray[0]);
			$tmpArray[0] = $temp[1];
			$dataArray[$tmpArray[0]] = $tmpArray[1];

			//给私有变量赋值
			switch ($tmpArray[0]) {
				case 'city': 
					$this->city = $tmpArray[1];
					break;
				case 'province': 
					$this->province = $tmpArray[1];
					break;
				case 'country': 
					$this->country = $tmpArray[1];
					break;
				case 'isp': 
					$this->isp = $tmpArray[1];
					break;
				case 'city': 
					$this->city = $tmpArray[1];
					break;
				case 'start': 
					$this->startIp = $tmpArray[1];
					break;
				case 'end': 
					$this->endIp = $tmpArray[1];
					break;
				default:
					# code...
					break;
			}
		}

		return $dataArray;
	}

	//获得用户ip
	private function get_onlineip() {
	    $onlineip = '';
	    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
	        $onlineip = getenv('HTTP_CLIENT_IP');
	    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
	        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
	    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
	        $onlineip = getenv('REMOTE_ADDR');
	    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
	        $onlineip = $_SERVER['REMOTE_ADDR'];
	    }
	    return $onlineip;
	}

	//返回省份
	public function getProvince() {
		return $this->province;
	}

	//返回城市
	public function getCity() {
		return $this->city;
	}

	//返回国家
	public function getCountry() {
		return $this->country;
	}

	//返回isp
	public function getISP() {
		return $this->isp;
	}

	//返回ip
	public function getIp() {
		return $this->ip;
	}
}


?>