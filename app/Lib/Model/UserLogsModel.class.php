<?php

class UserLogsModel extends Model {
    //商户操作记录函数
    public function userLogs($eventCode, $eventData) {
        $userId = session("uid");
        $this->add(array("user_id" => $userId, "event_code" => $eventCode, "event_data" => $eventData, "date" => date("Y-m-d H:i:s")));
    } 
}

?>