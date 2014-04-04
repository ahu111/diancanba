<?php

class OrderModel extends Model {
    //获取最近的任意条订单
    /* 用户需要登录
     * $number 要获取的条数
     */
    public function getUserOrderListByNumber($offset = 0, $number, $userId) {
        
        $orderData = $this->order("date DESC")->limit($offset, $number)->where("$userId = user_id")->field("oid, shop_id, order_price, order_status, date")->select();
        
        /* 1 根据把店铺的id来获取商铺名字
         */
        foreach ($orderData as $key => $value) {
            $shopInfo = D("Shop")->getShopDetailByShopId($value['shop_id']);
            $orderData[$key]['shop_name'] = $shopInfo['shop_name'];
        }
        return $orderData;
    }
    
    //根据订单的id获取订单的详情
    public function getOrderDetailByOrderId($orderId) {
        $orderDetail = $this->where("oid = $orderId")->find();
        /* 1 根据把店铺的id来获取商铺名字
         * 2 json数据转化为数组
         */
        $shopInfo = D("Shop")->getShopDetailByShopId($orderDetail['shop_id']);
        $orderDetail['shop_name'] = $shopInfo['shop_name'];
        $orderDetail['shop_phone'] = $shopInfo['shop_phone'];
        $orderDetail['shop_image'] = $shopInfo['shop_image'];
        $orderDetail['shop_introduce'] = $shopInfo['shop_introduce'];
        
        return $orderDetail;
    }
    
    //取消订单
    public function cancelOrderByOrderId($orderId, $userResponce) {
        //用户登录状态下
        $uid = session("uid");
        $data['order_status'] = 0;      //订单取消的数据
        $data['user_cancel_reason'] = $userResponce;
        $code = $this->where("oid = $orderId AND $uid = user_id")->save($data);

        if ($code) {
            $responce['code'] = 1;
            $responce['message'] = "订单取消成功，欢迎继续购买!";
        } else {
            $responce['code'] = 0;
            $responce['message'] = "订单取消失败，请刷新后确认订单是否异常!";
        }

        return $responce;
        
    }
    
     //确认订单
    public function confirmOrderByOrderId($orderId) {
       
        if (session("uid")) {
            $uid = session("uid");
            $data['order_status'] = 1;      //确认的数据
            $code = $this->where("oid = $orderId AND $uid = user_id")->save($data);
            
            if ($code) {
                $responce['code'] = 1;
                $responce['message'] = "订单确认成功，欢迎下次继续购买!";
            } else {
                $responce['code'] = 0;
                $responce['message'] = "订单确认失败，请刷新后确认订单是否异常!";
            }

            return $responce;
        }
    }
    
    //增加订单
    public function addOrder($remark = "") {
        $orderList = session("order");
        $uid = session("uid");
        
        $userInfo = D("User")->getUserInfoByUserId($uid);
        
        $insertData['shop_id'] = decodeShopId($orderList['shopId']);
        $insertData['order_price'] = $orderList['totalPrice'];
        $insertData['order_number'] = $orderList['totalNumber'];
        $insertData['order_address'] = $userInfo['address'];
        $insertData['order_phone'] = $userInfo['phone'];
        $insertData['order_user_name'] = $userInfo['user_name'];
        $insertData['order_pay_type'] = $orderList['payType'];
        $insertData['user_remark'] = $remark;
        $insertData['order_status'] = 2;
        $insertData['date'] = date("Y-m-d H:i:s");
        $insertData['user_id'] = $uid;
        
        $orderDetail = array();
        
        //提取session的订单详情
        foreach ($orderList as $key => $tmpValue) {
            if ($tmpValue['menuId'] != null && is_array($tmpValue)) {
              $tmpMenu['menu_id'] = decodeMenuId($tmpValue['menuId']);
              $tmpMenu['menu_name'] = $tmpValue['menuName'];
              $tmpMenu['menu_price'] = $tmpValue['menuPrice'];
              $tmpMenu['menu_number'] = $tmpValue['menuCount'];
              array_push($orderDetail, $tmpMenu);
            }
        }
        
        $orderId = $this->add($insertData);
        if ($orderId) {
            $orderDetailInsert = M("order_detail");
            foreach($orderDetail as $key => $value) {
                $value['order_id'] = $orderId;
                $orderDetailInsert->add($value);
            }
        }
        
        return $orderId;
    }
    
       //商店顾客详情
    public function getCustomInfoByDate() {
        $shopId = session("shopId");
        return $this->where("shop_id = $shopId AND order_status != 0")->group("user_id")->order("date DESC")->field("COUNT(*) AS order_sum, SUM(order_price) AS order_total_price, oid, order_user_name, date, user_id")->select();
    }
    
       //商店顾客详情
    public function getCustomInfoByAmount() {
        $shopId = session("shopId");
        return $this->where("shop_id = $shopId AND order_status != 0")->group("user_id")->order("order_total_price DESC")->field("COUNT(*) AS order_sum, SUM(order_price) AS order_total_price, oid, order_user_name, date, user_id")->select();
    }
    
       //商店顾客详情
    public function getCustomInfoByCount() {
        $shopId = session("shopId");
        return $this->where("shop_id = $shopId AND order_status != 0")->group("user_id")->order("order_sum DESC")->field("COUNT(*) AS order_sum, SUM(order_price) AS order_total_price, oid, order_user_name, date, user_id")->select();
    }
    
    //获取用户的详情订单
    public function getUserOrderByUserId($shopId, $userId) {
        return $this->where("shop_id = $shopId AND user_id = $userId")->order("date DESC")->field("oid, order_user_name, date, user_id, order_price, order_number, order_status, order_phone, user_remark, order_address")->select();
    }

    //获取一定时间内未评价的订单
    public function getOrderUnEvaluateInTime($userId, $time) {
        return M()->table("o_order_detail, o_order")->where("o_order.user_id = $userId AND o_order_detail.is_grade = 0 AND o_order.oid = o_order_detail.order_id AND o_order.order_status = 1")->having("TO_DAYS(NOW()) - TO_DAYS(o_order.date) < $time")->select();
    }
    
    //获得等待确认和正在配送的订单 一天之内  正在送的 和 未确认的
    public function getNewOrderList($shopId) {
        return $this->where("shop_id = $shopId AND (order_status = 2 OR order_status = 3) AND to_days(NOW()) - to_days(date) = 0")->limit(20)->order("date DESC")->select();
    }
    
    //确认订单和商家是否相匹配
    public function boolOrderWithShop($shopId, $orderId) {
        return !!($this->where("shop_id = $shopId AND oid = $orderId")->find());
    }
    
    
    /****管理员*********/
    //获取最新的订单
    public function getNewOrderByAdmin() {
         return M()->table("o_order as o, o_shop as s")->where("o.order_status = 2 AND o.shop_id = s.shop_id  AND to_days(NOW()) - to_days(date) = 0")
                 ->order("date DESC")->select();
    }
    
    //检验新的订单是否存在
    public function checkNewOrder($shopId) {
        return $this->where("shop_id = $shopId AND order_status = 2 AND to_days(now()) - to_days(date) = 0")->find();
    }
    
    //检验新的订单是否存在
    public function checkNewOrderByAdmin() {
        return $this->where("order_status = 2  AND to_days(now()) - to_days(date) = 0")->find();
    }
}

?>
