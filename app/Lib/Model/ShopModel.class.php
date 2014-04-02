<?php

class ShopModel extends Model {
    
    //获取某个具体地区的店铺列表
    public function getShopListByArea() {
        return $this->where("shop_status <> 0")->select();
    }
    
    /* 根据shopId获取shop的具体信息
     * 返回店铺的条件为： 1.店铺未关闭 2.存在该店铺的id
     */
    public function getShopDetailByShopId($shopId) {
        return $this->where("shop_id = $shopId")->find();
    }
        
    public function shopUserLogin($shopEmail, $shopPwdEncode) {
        return $this->where("shop_email = '$shopEmail' AND shop_password = '$shopPwdEncode'")->find();
    }
    
    /*
     * 获取商铺起送价
     */
    public function getShopStartPrice($shopId) {
        return $this->where("shop_id = $shopId")->limit(1)->getField("shop_start_price");
    }
    
    //获取店铺状态
    public function getShopStatus($shopId) {
        return $this->where("shop_id = $shopId")->getField("shop_status");
    }
    
    //添加商户
    public function addShop($email, $pwdEncode) {
        return $this->add(array("shop_email" => $email, "shop_password" => $pwdEncode, "shop_grade" => 0, 
            "shop_image" => "/static/img/v1/shop_logo_default.png", "shop_introduce" => "餐厅暂无介绍",
            "shop_notify" => "餐厅暂无通知", "shop_open_time" => "暂未填写营业时间", "shop_phone" => "",
            "shop_grade_people" => 0, "shop_people_visit" => 0, "shop_start_price" => 0, "shop_status" => 2));
    }
    
    //检查商户是否已经存在根据邮箱
    public function checkUserExist($email) {
        return $this->where("shop_email = '$email'")->find();
    }
    
    
    /*
     * 面向商户的函数
     */
    
    //店铺关闭也显示
    public function getShopInfoByShopId($shopId) {
        return $this->where("shop_id = $shopId")->find();
    }
    
    public function getShopName($shopId) {
        return $this->where("shop_id = $shopId")->getField("shop_name");
    }
    
    public function shopUserInfoUpdate($shopUserName, $shopUserPhone, $shopSales) {
        $shopId = session("shopId");
        return $this->where("shop_id = $shopId")->save(array("shop_user_name" => $shopUserName,
                "shop_user_phone" => $shopUserPhone, "shop_sales_level" => $shopSales));
    }
    
    //商铺用户有效性验证
    public function shopUseLogin($shopEmail, $shopPassword) {
        return $this->where("(shop_email = '$shopEmail' || shop_phone = '$shopEmail') AND shop_password = '$shopPassword'");
    }
    
    //验证用户密码的正确性
    public function checkPassword($shopId, $shopPassword) {
        return $this->where("shop_id = $shopId AND shop_password = '$shopPassword'")->find();
    }
    
    //商户密码修改
    public function updatePassword($shopId, $shopNewPassword) {
        return $this->where("shop_id = $shopId")->save(array("shop_password" => $shopNewPassword));
    }
    
    //获取商铺的送餐速度
    public function getShopSpeed($shopId) {
        return $this->where("shop_id = $shopId")->getField("shop_speed");
    }
    
    //获取商铺的电话
    public function getShopPhone($shopId) {
        return $this->where("shop_id = $shopId")->getField("shop_phone");
    }
    
   
}
?>
