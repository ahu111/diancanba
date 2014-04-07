<?php

/*
 * 管理员页面
 */

class AdminAction extends Action {
    
    public function __construct() {
        R("Public/session_start_by_user");
    }
    
    public function index() {
        session_start();
        //判断是否登录
        if (!session("admin")) {
            $this->redirect("login");
        }     
        

        $this->display();
    }
    
    
    //商户管理
    public function shop() {
        //判断是否登录
        session_start();
        if (!session("admin")) {
            $this->redirect("login");
        }     
        $shopList = M("Shop")->select();
        $this->assign("shopList", $shopList);
        $this->display();
    }
    
    //添加商户
    public function addShop() {
        session_start();
        $email = $this->_post("email");
        $regxMail = "/^[a-zA-Z0-9-._]{1,50}@[a-zA-Z0-9-]{1,65}.(com|net|org|info|biz|([a-z]{2,3}.[a-z]{2}))$/i";
        $password = "11111111";  //初始密码8个1
        //验证邮箱正确性
        if (preg_match($regxMail, $email)) {
            if (D("Shop")->checkUserExist($email)) {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "添加失败，该邮箱已经存在！"));
            }
            
            $shopId = D("Shop")->addShop($email, sha1(md5($password)));
            if ($shopId) {
                mkdir("data/shop/".$shopId, 0777, true);        //创建商铺文件夹
                mkdir("data/menu/".$shopId, 0777, true);        //创建商铺菜单文件夹
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "商户添加成功！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "添加失败，请检查您的输入是否有问题！"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "邮箱格式错误！"));
        }
    }
    

      //移动端获取新的订单信息
    public function get_new_menu_from_mobile_by_admin() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();

        $orderList = D("Order")->getNewOrderByAdmin();
        $detailModel = M("Order_detail");

        //给每个订单添加具体的菜单
        foreach($orderList as $key => $value) {
            $orderId = $value['oid'];
            $orderList[$key]['menuList'] = $detailModel->where("order_id = $orderId")->select();
        }

        $checkWhetherHaveNewOrder = D("Order")->checkNewOrderByAdmin();

        if ($checkWhetherHaveNewOrder) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "data" => $orderList));
       } else {
            $this->ajaxReturn(array("responce" => "FAILED",  "data" => $orderList));
       }
    }

    //确认订单页面
    public function order() {
        session_start();
        //判断是否登录
        if (!session("admin")) {
            $this->redirect("login");
        }     
        
        $orderList = D("Order")->getNewOrderByAdmin();

        $detailModel = M("Order_detail");
        
        //给每个订单添加具体的菜单
        foreach($orderList as $key => $value) {
            $orderId = $value['oid'];
            $orderList[$key]['menuList'] = $detailModel->where("order_id = $orderId")->select();
        }
        
        
        $this->assign("orderList", $orderList);

        $this->display();
        
    }

    
    
    //管理员登录验证
    public function adminLogin() {
        session_start();
        $userAccount = $this->_post("userEmail");
        $userPassword = $this->_post("userPassword");
  
        $regxAccount = "/^[a-zA-Z0-9_]{6,16}$/i";
        $regxPassword = "/^[a-zA-Z0-9_-]{6,20}$/i";
        
        if (!preg_match($regxAccount, $userAccount)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "账号格式错误！"));
        }
        
        if (!preg_match($regxPassword, $userPassword)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "密码格式错误！"));
        }
        
        if ($flag = D("Admin")->adminLogin($userAccount, sha1(md5($userPassword)))) {
            session_set_cookie_params(3600 * 24, "/");
            session('[regenerate]');
            session("admin", $flag['id']);              //用户的id
            
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "登陆成功！", "data" => session_id()));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "账号不存在或者密码错误！"));
        }
    }
    
    //退出
    public function login_out() {
        session_start();
        session("destroy");
        $this->redirect("login");
    }
    
    //检查新订单
    public function checkNewOrderByAdmin(){
        session_start();
        if (session("admin")) {
            $checkWhetherHaveNewOrder = D("Order")->checkNewOrderByAdmin();
            M("Shop")->where("shop_status = 2")->save(array("scan_time" => date("Y-m-d H:i:s"), "shop_status" => 1));
            if ($checkWhetherHaveNewOrder) {
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "您有新的订单，请刷新本页后查看！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "暂无新的订单"));
            }
        }
    }
    

    //管理员确认订单
    public function acceptOrderByAdmin() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();
        
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        $orderAddress = $this->_post("orderAddress");
        $orderPhone = $this->_post("orderPhone");
        $orderUserName = $this->_post("orderUserName");
        $orderPrice = $this->_post("orderPrice");
        $shopId = M("order")->where("oid = $orderId")->getField("shop_id");

        $shopSpeed = D("Shop")->getShopSpeed($shopId);
            
        //确认发送短信
       // if ($this->sendMessageToUser($orderPhone, $orderUserName, $orderAddress, $shopSpeed, $orderPrice)) {
            M("order")->where("oid = $orderId")->save(array("order_status" => 3));      //确认订单
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "订单已成功确认！"));
       //}
    }
    
    //管理员拒绝订单
    public function rejectOrderByAdmin() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        $reason = $this->_post("reason");
        $orderPhone = $this->_post("orderPhone");
        $orderUserName = $this->_post("orderUserName");
        $shopId = M("order")->where("oid = $orderId")->getField("shop_id");
        
        if (D("Order")->boolOrderWithShop($shopId, $orderId)) {
         //   if ($this->sendRejectMessageToUser($orderPhone, $orderUserName, $reason)) {
                M("order")->where("oid = $orderId")->save(array("order_status" => 4, "reject_reason" => $reason));      //确认订单
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "拒绝成功！"));
           // }
            
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您的餐厅没有该订单，请刷新后重试！"));
        }
    }

    //向用户发送确认订单短信
    private function sendMessageToUser($userPhone, $userName, $userAddress, $shopSpeed, $orderPrice) {
        vendor("sms");
        $sms = new sms();
        $msg = $userName . "，您的订单餐厅已确认，将在大约" . $shopSpeed . "左右后送达至" . $userAddress .
                ",请备好零钱" . "$orderPrice" . "，随时保持您的电话畅通【点餐吧】";
     
        $flag = $sms->sendnote($userPhone, $msg);
        
        if ($flag != 1) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "该手机号码无效！您可再确认一次！"));
        } 
        return true;
    } 
    
    //向用户发送拒绝订单短信
    private function sendRejectMessageToUser($userPhone, $userName,  $reason) {
        vendor("sms");
        $sms = new sms();
        $msg = $userName . "，您的订单商家被商家拒绝，商家拒绝理由为：" . $reason . "。欢迎您继续使用【点餐吧】";

        $sms->sendnote($userPhone, $msg);
        return true;
    } 
}
