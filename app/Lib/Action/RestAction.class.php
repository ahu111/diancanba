<?php

class RestAction extends CommonAction {
    /*
     * 餐厅列表页面
     */
    public function index() {
        $shopList = D("Shop")->getShopListByArea();
        
        $shopOpen = array();        //营业中的店铺
        $shopRest = array();        //休息中的店铺
        $shopClose = array();       //关闭的店铺
        $shopPause = array();       //餐厅打烊
        
        foreach($shopList as $key => $value) {
            switch ($value['shop_status']) {
                case 1: array_push($shopOpen, $value);break;
                case 2: array_push($shopPause, $value);break;
                case 3: array_push($shopRest, $value);break;
                case 0: array_push($shopClose, $value);break;
            }
        }
        
        $shopList = array_merge($shopOpen, $shopRest, $shopPause, $shopClose);
        
        
        $this->assign("shopList", $shopList);  
        R("Public/recordUserVisited");          //记录
        $this->display("list");
    }
    
    /*
     * 餐厅详情
     */
    public function detail() {
        $shopId = decodeShopId(intval($this->_param(2)));         //未解密的
        $shopDetail = D("Shop")->getShopDetailByShopId($shopId);
        $menuList = D("Menu")->getMenuListByShopId($shopId);
        $uid = session("uid");
        
        if (is_array($shopDetail)) {
            $favoriteStatus = M("shop_favorite")->where("shop_id = $shopId AND user_id = $uid")->find();       //获取是否缓存
            $this->assign("shopDetail", $shopDetail);
            $this->assign("menuList", $menuList);
            $this->assign("favoriteStatus", $favoriteStatus);
            
            R("Public/recordUserVisited");          //记录
            
            //添加商铺访问记录
            M("shop_uv_logs")->add(array("shop_id" => $shopId, "user_id" => $uid, "date" => date("Y-m-d H:i:s")));
            
            $this->display();
        } else {
            $this->assign("title", "错误提示");
            $this->assign("message", "不存在该页面");
            $this->assign("jumpUrl", "/rest");
            $this->error();
        }
    }
    
    /*
     * 订单改变/增加/减少
     */
    public function orderChange() {
        $menuId = intval($this->_post("menuIndex"));  //菜单的id
        $shopId = intval($this->_post("shopIndex"));  //店铺的id
        $operator = $this->_post("operator");   //所要进行的操作
       
        $responceBool = D("Menu")->boolShopIdWithMenuId(decodeShopId($shopId), decodeMenuId($menuId));
        $returnNumber = 0;
        
        //对应的店铺跟菜单是对应的
        if (is_array($responceBool)) {
            
            //确认该菜单还没有售罄 还有返回1  售罄了 就返回0
            if (D("Menu")->getMenuStatus(decodeMenuId($menuId)) == 2) {
                //店铺中不存在该商品
                $returnResponce['message'] = "该餐点已售罄";
                $returnResponce['responce'] = "FAILED";
                $this->ajaxReturn($returnResponce);
            }
            
            $shopStatus = D("Shop")->getShopStatus(decodeShopId($shopId));
            
            //查看店铺状态
            if ($shopStatus == 2) {
                $returnResponce['message'] = "餐厅已经打烊";
                $returnResponce['responce'] = "FAILED";
                $this->ajaxReturn($returnResponce);
            } else if ($shopStatus == 3) {
                $returnResponce['message'] = "餐厅正在休息中，订餐请稍候！";
                $returnResponce['responce'] = "FAILED";
                $this->ajaxReturn($returnResponce);
            }
            
            $orderList = session("order");
            //当用户的order不是空的时候
            if (is_array($orderList)) {
                if ($orderList['shopId'] != $shopId) {
                    session("order", null);
                    $orderList['shopId'] = $shopId;
                    $orderList['shopStartPrice'] = intval(D("Shop")->getShopStartPrice(decodeShopId($shopId))); //获取商铺起送价
                    $orderList['totalPrice'] = 0;
                    $orderList['totalNumber'] = 0;
                    $orderList['payType'] = 0;  //货到付款
                }
            } else {
                $orderList['shopId'] = $shopId;
                $orderList['shopStartPrice'] = intval(D("Shop")->getShopStartPrice(decodeShopId($shopId))); //获取商铺起送价
                $orderList['totalPrice'] = 0;
                $orderList['totalNumber'] = 0;
                $orderList['payType'] = 0;  //货到付款
            }
            
            $tag = false;   //用来标记该餐品是否被找到
            $totalPrice = $orderList['totalPrice'];        //物品总价
            
            //在session中寻找对应的menu_id的数量、如果没有则为0
            foreach ($orderList as $key => $tmpValue) {
                if ($menuId == $tmpValue['menuId']) {
                    $tag = true;
                    if ($operator === "MINUS" && $tmpValue['menuCount'] > 0) {
                        $tmpValue['menuCount'] -= 1;
                        $orderList['totalNumber'] -= 1;
                        $totalPrice -= $tmpValue['menuPrice'];
                        $orderList[$key] = $tmpValue;
                        
                        if ($tmpValue['menuCount'] == 0) {
                            $orderList[$key] = null;
                        }
                        
                    } else if ($operator == "PLUS") {
                        $tmpValue['menuCount'] += 1;
                        $orderList['totalNumber'] += 1;
                        $totalPrice += $tmpValue['menuPrice'];
                        $orderList[$key] = $tmpValue;
                        
                    } else if ($operator == "MINUS" && $tmpValue['menuCount'] <= 0) {
                        $tmpValue['menuCount'] = 0;
                        $orderList[$key] = null;
                        
                    }
                    
                    $returnNumber = $tmpValue['menuCount'];     //将要返回的数字
                } else if (!($tmpValue['menuId'])){
                    $totalPrice += $tmpValue['menuPrice'] * $tmpValue['menuCount'];
                }
            }

            //如果该商品没有被订购则添加
            if ($tag == false && $operator == "PLUS") {
                $menuInfo = D("Menu")->getMenuInfoByMenuId(decodeMenuId($menuId));
                array_push($orderList, array("menuId" => $menuId,
                    "menuCount" => 1, "menuPrice" => $menuInfo['price'],
                    "menuName" => $menuInfo['menu_name']));
                $totalPrice += $menuInfo['price'];
                $returnNumber = 1;
                $orderList['totalNumber'] += 1;
            }
            
            $orderList['totalPrice'] = $totalPrice;
            
            if ($orderList['totalNumber'] == 0) {
                session("order", null);
            } else {
                session("order", $orderList);
            }
              
            $this->ajaxReturn(array("responce" => "SUCCESS", "orderInfo" => $orderList, "responceNumber" => $returnNumber));
        } else {
            //店铺中不存在该商品
            $returnResponce['message'] = "该餐厅没有该餐点, 请刷新后确认!";
            $returnResponce['responce'] = "FAILED";
            $this->ajaxReturn($returnResponce);
        }
    }
    
    //初始化订单
    public function initOrder() {
        $orderList = session('order');
        $shopId = intval($this->_post("shopIndex"));
        //当存在订单
        if (is_array($orderList)) {
            if ($orderList['shopId'] == $shopId) {
                $this->ajaxReturn(array("responce" => "SUCCESS", "orderInfo" => $orderList));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED"));
        }
    }
    
    /*
     * 订单提交
     */
//    public function orderCheck() {
//        $this->ajaxReturn(array("responce" => "SUCCESS"));
//    }
    public function orderSubmit() {
        $orderList = session("order");
        
        //存在订单
        if (is_array($orderList)) {
            if ($orderList['shopStartPrice'] <= $orderList['totalPrice']) {
                $remark = $this->_post("userRemark");    //用户留言
                $userInfo = D("User")->getUserInfoByUserId(session("uid"));
                
                //确认订单的地址和电话正确
                if ($userInfo['phone'] == "" || $userInfo['address'] == "") {
                    $responce['responce'] = "FAILED";
                    $responce['message'] = '请补充您的电话/地址后再次添加';
                    $this->ajaxReturn($responce);
                }
                
               $flag = D("Order")->addOrder($remark);
               if ($flag != null) {
                   $responce['responce'] = "SUCCESS";
                   $responce['message'] = '提交成功,正在跳转...';
                   session("order", null);
               } else {
                   $responce['responce'] = "FAILED";
                   $responce['message'] = '提交失败,请检查您的订单是否有误!';
               }
               
               $this->ajaxReturn($responce);
            } else {
                echo "您所订的餐点未达到起送价,请返回后确认重试.";
            }
        } 
        
        $this->ajaxReturn(array("responce" => "SUCCESS"));
    }
    
    
    /*
     * 确认订单页面
     */
    public function order() {
        $orderList = session("order");
        
        
        //存在订单
        if (is_array($orderList)) {
            if ($orderList['shopStartPrice'] <= $orderList['totalPrice']) {
                //当超过了起送价
                $shopId = decodeShopId($orderList['shopId']);
                $shopDetail = D("Shop")->getShopDetailByShopId($shopId);
                $userInfo = D("User")->getUserInfoByUserId(session("uid"));
                
                $this->assign("userInfo", $userInfo);
                $this->assign("shopDetail", $shopDetail);
                $this->assign("orderList", $orderList);
                
                R("Public/recordUserVisited");          //记录
                $this->display();
            } else {
                $this->assign("title", "错误提示");
                $this->assign("message", "您所订的餐点未达到起送价");
                $this->assign("jumpUrl", "/rest/detail/".$orderList['shopId']);
                $this->error();
            }
        } else {
            $this->assign("title", "错误提示");
            $this->assign("message", "完成菜单的选择才能访问此页面");
            $this->assign("jumpUrl", "/rest");
            $this->error();
        }
    }
    
    //收藏店铺
    public function favorite () {
        $shopId = decodeShopId(intval($this->_param(2)));
        $userId = session("uid");
        if (M('shop_favorite')->where("user_id = $userId AND shop_id = $shopId")->find()) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您已收藏该餐厅，请刷新后重试"));
        } else {
            if (M("shop_favorite")->add(array("user_id" => $userId, "shop_id" => $shopId, "date" => date("Y-m-d H:i:s")))) {
                D("UserLogs")->userLogs(10004, json_encode(array("shop_id" => $shopId)));            //用户收藏餐厅操作记录
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅收藏成功！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "收藏失败，请刷新后重试"));
            }
        }
    }
    
    //取消收藏店铺
    public function favorite_remove () {
        $shopId = decodeShopId(intval($this->_param(2)));
        $userId = session("uid");
        if (M('shop_favorite')->where("user_id = $userId AND shop_id = $shopId")->find()) {
            if (M('shop_favorite')->where("user_id = $userId AND shop_id = $shopId")->delete()) {
                D("UserLogs")->userLogs(10005, json_encode(array("shop_id" => $shopId)));            //用户收藏餐厅操作记录
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅取消收藏成功！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "取消失败，请刷新后重试"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您还未收藏该餐厅！"));
        }
    }
}
?>
