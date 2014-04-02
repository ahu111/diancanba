<?php

/*
 * 管理员页面
 */

class AdminAction extends Action {
    
    public function __construct() {
        R("Public/session_start_by_user");
        session_start();
    }
    
    public function index() {
        
        //判断是否登录
        if (!session("admin")) {
            $this->redirect("login");
        }     
        

        $this->display();
    }
    
    
    //商户管理
    public function shop() {
        //判断是否登录
        if (!session("admin")) {
            $this->redirect("login");
        }     
        $shopList = M("Shop")->select();
        $this->assign("shopList", $shopList);
        $this->display();
    }
    
    //添加商户
    public function addShop() {
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
    
    //确认订单页面
    public function order() {
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
        $userAccount = $this->_param(2);
        $userPassword = $this->_param(3);
        
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
            
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "登陆成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "账号不存在或者密码错误！"));
        }
    }
    
    //退出
    public function login_out() {
        session("destroy");
        $this->redirect("login");
    }
    
    //检查新订单
    public function checkNewOrderByAdmin(){
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
    
    //接受订单并通知商家
    public function acceptOrderByAdmin(){
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        
        Vendor("sms");
        $sms = new sms();
        $flag = $sms->sendnote("15521377948", "1231121【点餐吧】");
        
        //M("order")->where("oid = $orderId")->save(array("order_status" => 3));      //确认订单
        $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "订单已成功确认！$flag"));
    }
}
