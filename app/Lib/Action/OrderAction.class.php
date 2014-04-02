<?php

class OrderAction extends CommonAction {

    /*
     * 显示我的订单列表
     * 默认只显示本月的订单列表 按更多则显示上一个月 
     */
    public function index() {
        
        //从偏移量0开始 获取十条
        $uid = session("uid");

        $data = D("Order")->getUserOrderListByNumber(0, 10, $uid); 

        //订单状态的配置
        $orderStatus = D("system_config")->where("set_type = 'order_status'")->order("set_code ASC")->select(); 
        $this->assign("orderStatus", $orderStatus);

        $this->assign("orderList", $data);          
        
        R("Public/recordUserVisited");          //记录
        $this->display("list");
    }
    
    /*
     * 订单的具体内容
     */
    public function detail() {
        $orderId = decodeOrderId(intval($this->_param(2)));
        
        //订单状态的配置
        $orderStatus = D("system_config")->where("set_type = 'order_status'")->order("set_code ASC")->select(); 
        $this->assign("orderStatus", $orderStatus);
        
        $data = D("Order")->getOrderDetailByOrderId($orderId);
        $data['order_detail'] = M("order_detail")->where("order_id = $orderId")->select();
        
        $this->assign("orderDetail", $data);
        
        R("Public/recordUserVisited");          //记录
        $this->display();
    }
    
    //订单取消的函数
    public function orderCancel() {
        $orderId = decodeOrderId(intval($this->_post("orderIndex")));     //获取正确id
        $userResponce = $this->_post("userResponce");
        
        $orderCancelResponce = D("Order")->cancelOrderByOrderId($orderId, $userResponce);
        $this->ajaxReturn($orderCancelResponce);
    }
    
     //订单确认的函数
    public function orderConfirm() {
        $orderId = decodeOrderId(intval($this->_post("orderIndex")));     //获取正确id
        $orderConfirmResponce = D("Order")->confirmOrderByOrderId($orderId);
        $this->ajaxReturn($orderConfirmResponce);
    }
    
    //用户评价赢积分
    public function evaluate() {
        $userId = session("uid");
        $time = M("System_config")->where("set_type = 'confirm_order_time'")->find();
        
        //获得内未评价的商品 //时间过后自动转为已完成
        $orderList = D("Order")->getOrderUnEvaluateInTime($userId, $time['set_code']);        
        $this->assign("orderList", $orderList);
        
        R("Public/recordUserVisited");          //记录
        $this->display();
    }
    
    
    //菜单评分
    public function menuGrade() {
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        $grade = floatval($this->_post("grade"));        //分数
        $remark = $this->_post("remark");         //留言
 
        if (D("order_detail")->where("menu_id = $menuId AND order_id = $orderId AND is_grade = 0")->save(array("is_grade" => 1,
            "grade" => $grade, "remark" => $remark))) {
            $uid = session("uid");
            
            D("User")->addUserEvaluateCredit($orderId, $menuId);         //增加用户积分
            D("UserLogs")->userLogs(10000, json_encode(array("order_id" => $orderId, "menu_id" => $menuId)));
            
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "评分成功！"));
            
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "请先确认该订单是否存在！"));
        }
        
    }
    
    
}

?>
