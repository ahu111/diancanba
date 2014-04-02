<?php

class ShopLogsModel extends Model {
    //商户操作记录函数
    public function shopLogs($eventCode, $eventData) {
        $shopId = session("shopId");
        $this->add(array("shop_id" => $shopId, "event_code" => $eventCode, "event_data" => $eventData, "date" => date("Y-m-d H:i:s")));
    } 
}

?>