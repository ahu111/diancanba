<?php

class AreaModel extends Model {
    
    //获取地区的名字
    public function getAreaInfo($areaId) {
        return $this->where("area_id = $areaId")->field("pid, area_name")->find();
    }
    
    //获取一个id下的所有地区
    public function getAreaList($pid) {
        return $this->where("pid = $pid")->field("area_id, area_name")->select();
    }
}
