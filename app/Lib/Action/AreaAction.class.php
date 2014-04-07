<?php

class AreaAction extends Action {
    
    public function __construct() {
        R("Public/session_start_by_user");
        session_start();
    }
    //选择地区
    public function index() {
        //如果已经地址的记录了就显示该地址
        if (session("areaId")) {
            $areaId = session("areaId");                //最小级  精确到具体的地方
            $areaInfo = D("Area")->getAreaInfo($areaId);            
            $areaDistrictId = $areaInfo['pid'];          //上一级的id         //一个小范围   比如番禺大学城
            
            $areaUpstairInfo = D("Area")->getAreaInfo($areaDistrictId);
            $areaCityId = $areaUpstairInfo['pid'];         //再上一级的id    对应广州了  
            
            $areaDoubleUpstairInfo = D("Area")->getAreaInfo($areaCityId);
            $areaProvinceId = $areaDoubleUpstairInfo['pid'];         //再上一级的id    对应广东省了    省级以上的pid都是0
            
            $provinceList = D("Area")->getAreaList(0);            //具体的列表
            $cityList = D("Area")->getAreaList($areaProvinceId);            //城市列表
            $districtList = D("Area")->getAreaList($areaCityId);        //地域列表   英文不好别怪我
            $areaList = D("Area")->getAreaList($areaDistrictId);            //具体的列表
            
            
            $this->assign("areaList", $areaList);
            
            $this->assign("provinceList", $provinceList);
            $this->assign("provinceId", $areaProvinceId);
            
            $this->assign("districtId", $areaDistrictId);
            $this->assign("districtList", $districtList);
            
            $this->assign("cityList", $cityList);
            $this->assign("cityId", $areaCityId);

            $this->assign("areaName", $areaInfo['area_name']);
        } else {
            //第一期已经默认选择了前三栏  只从选择附近地点开始就好
            //番禺大学城的  area_id = 3

            if (session("districtId") === null) {
                session("districtId", 3);           //番禺大学城
            }
            
            if (session("cityId") === null) {
                session("cityId", 2);               //城市
            }
            
            if (session("provinceId") === null) {
                session("provinceId", 1);           //省份  广东
            }

            $areaDistrictId = session("districtId");          //上一级的id         //一个小范围   比如番禺大学城
            $areaCityId = session("cityId");         //再上一级的id    对应广州了  
            $areaProvinceId = session("provinceId");         //再上一级的id    对应广东省了    省级以上的pid都是0
           
            //检查省份与城市是否匹配
            $tmp = D("Area")->getAreaInfo($areaCityId);
            if ($tmp['pid'] == $areaProvinceId) {
                $cityList = D("Area")->getAreaList($areaProvinceId);            //城市列表
                $this->assign("cityList", $cityList);
                $this->assign("cityId", $areaCityId);
                
                //检查区域与城市是否匹配
                $tmp2 = D("Area")->getAreaInfo($areaDistrictId);
                if ($tmp2['pid'] == $areaCityId) {
                    $districtList = D("Area")->getAreaList($areaCityId);        //地域列表   英文不好别怪我
                    $this->assign("districtId", $areaDistrictId);
                    $this->assign("districtList", $districtList);
                    
                    //检查附近的地点与区域是否匹配
                    $tmp2 = D("Area")->getAreaInfo($areaDistrictId);
                    if ($tmp2['pid'] == $areaCityId) {
                        $areaList = D("Area")->getAreaList($areaDistrictId);            //具体的列表
                        $this->assign("areaList", $areaList);
                    } 
                } 
            } else {
                $cityList = D("Area")->getAreaList($areaProvinceId);            //城市列表
                $this->assign("cityList", $cityList);
            }
   
            $provinceList = D("Area")->getAreaList(0);            //具体的列表
            $this->assign("provinceList", $provinceList);
            $this->assign("provinceId", $areaProvinceId);
        }
        $this->display();
    }
    
    //获得该select框所有的数据、需要拿到他上级的area_id
    public function getAreaList(){
        $flag = $this->_post("flag");
        $areaId = $this->_post("id");
        //获取附近低点
        if ($flag === "area") {
            //为了确认改地址的value值是正确的
            $distId = session("districtId");
            $tmp = D("Area")->getAreaInfo($areaId);
            if ($tmp['pid'] == $distId) {
                session("areaId", $areaId);
                $this->ajaxReturn(array("responce" => "SUCCESS"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "您输入的附近的地点值与该地区不匹配，请刷新后重试！"));
            }
            
        } else if ($flag === "dist") {
            //为了确认改地址的value值是正确的
            $cityId = session("cityId");
            $tmp = D("Area")->getAreaInfo($areaId);
            if ($tmp['pid'] == $cityId) {
                session("districtId", $areaId);
                session("areaId", null);
                $areaList = D("Area")->getAreaList($areaId);
                $this->ajaxReturn(array("responce" => "SUCCESS", "areaList" => $areaList));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "您输入的地区与该城市不匹配，请刷新后重试！"));
            }
        } else if ($flag === "city") {
            //为了确认改地址的value值是正确的
            $provinceId = session("provinceId");
            $tmp = D("Area")->getAreaInfo($areaId);
            if ($tmp['pid'] == $provinceId) {
                session("cityId", $areaId);
                session("districtId", null);
                session("areaId", null);
                $areaList = D("Area")->getAreaList($areaId);
                $this->ajaxReturn(array("responce" => "SUCCESS", "areaList" => $areaList));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "您选择的城市与该省份不匹配，请刷新后重试！"));
            }
        } else if ($flag === "province") {
            $tmp = D("Area")->getAreaInfo($areaId);
            if ($tmp) {
                session("provinceId", $areaId);
                session("cityId", null);
                session("districtId", null);
                session("areaId", null);
                $areaList = D("Area")->getAreaList($areaId);
                $this->ajaxReturn(array("responce" => "SUCCESS", "areaList" => $areaList));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "您选择的省份值不存在，请刷新后重试！"));
            }
            
        }
    }
    
    //点击提交后检查是否已经设置了areaId
    public function checkAreaId() {
        if (session("areaId")) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "areaId" => session("areaId"), "sessionId" => session_id()));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "请选择附近的地点后再进行确认"));
         }
    }

    /**
     * 功能：用户sessionId初始化
     */
    public function init() {
        $sessionId = $this->_post("sessionId");
        $areaId = $this->_post("areaId");
        session("name", "__wx__");
         session_id($sessionId);
        session_set_cookie_params(3600 * 24 * 365, "/");
        session('expire', 3600 * 24 * 365);   
       session_start();
       session("areaId", $areaId);
       $this->ajaxReturn(array("responce" => true));
    }
}