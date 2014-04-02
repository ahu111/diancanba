<?php
  //商家的函数
class ShopAction extends Action {
    
    public function __construct() {
        R("Public/session_start_by_user"); 
    }
    //商户主页
    public function index() {
        session_start();
        $shopId = session("shopId");
        if (!session("shopId")) {
            $this->redirect("/shop/login");
        }
        
        $orderList = D("Order")->getNewOrderList($shopId);
        
        $detailModel = M("Order_detail");;
        //给每个订单添加具体的菜单
        foreach($orderList as $key => $value) {
            $orderId = $value['oid'];
            $orderList[$key]['menuList'] = $detailModel->where("order_id = $orderId")->select();
        }
        
        $this->assign("orderList", $orderList);
        $this->display();
    }
    
    
    //商家登录页面
    public function login() {
        session_start();
        if (session("shopId")) {
            $this->redirect("/shop/");
        }
        
        $this->display();
    }
    
    //检验新的订单
    public function checkNewOrder() {
        session_start();
        $shopId = session("shopId");
        $checkWhetherHaveNewOrder = D("Order")->checkNewOrder($shopId);
        M("Shop")->where("shop_id = $shopId AND shop_status = 2")->save(array("scan_time" => date("Y-m-d H:i:s"), "shop_status" => 1));
        
        if ($checkWhetherHaveNewOrder) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "您有新的订单，请刷新本页后查看！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "暂无新的订单"));
        }
        
        
    }
    
    //餐厅登录函数
    public function shopLogin() {
        session_start();
        $shopEmail = $this->_post("userEmail");
        $shopPassword = $this->_post("userPassword");
        $regxEmail = "/^[a-zA-Z0-9-._]{1,50}@[a-zA-Z0-9-]{1,65}.(com|net|org|info|biz|([a-z]{2,3}.[a-z]{2}))$/i";
        $regxPassword = "/^[a-zA-Z0-9_]{6,20}$/i";
        
        //验证密码
        if (!preg_match($regxPassword, $shopPassword)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "密码格式错误！"));
        }
        
        //验证用户名
        if (!preg_match($regxEmail, $shopEmail)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "商户登录邮箱格式错误！"));
        }
        
        $flag = D("Shop")->shopUserLogin($shopEmail, sha1(md5($shopPassword)));            //登录验证
        
        if ($flag) {
            R("Public/session_start_by_user");  
            session_start();
            session("shopId", $flag['shop_id']);
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "登录成功！正在跳转...", "data" => session_id(), "shopName" => $flag['shop_name']));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "登录失败，邮箱或者密码错误！"));
        }
    }
    
    //商户退出函数
    public function shop_login_out() {
        session_start();
        session('[destroy]');
        if (!session("shopId")) {
            $this->redirect("/shop/login");
        }
    }
    
    //商户信息页面
    public function user() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }

        $saleLevel = M("SystemConfig")->where("set_type = 'shop_sales_level'")->order("set_code ASC")->select();
        $shopInfo = D("Shop")->getShopInfoByShopId($shopId);
        $this->assign("shopInfo", $shopInfo);
        $this->assign("saleLevel", $saleLevel);

        $this->display();
    }
    
    //商户信息更新函数
    public function shopUserInfoUpdate(){
        session_start();
        $shopUserName = $this->_param(2);
        $shopUserPhone = $this->_param(3);
        $shopSales = $this->_param(4);
        
        $flag = D("Shop")->shopUserInfoUpdate($shopUserName, $shopUserPhone, $shopSales);
        if ($flag == 0 || $flag == 1) {
            D("ShopLogs")->shopLogs(20001, json_encode(array($shopUserName, $shopUserPhone,$shopSales )));
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "更新成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "更新失败，请检查你的输入是否有误！"));
        }
    }
    
    //商户密码更新函数
    public function updateShopPassword() {
        session_start();
        $shopOldPassword = $this->_param(2);
        $shopNewPassword = $this->_param(3);
        $shopId = session("shopId");
        
        $regxPassword = "/^[a-zA-Z0-9_]{6,16}$/";
        //验证密码
        if (!preg_match($regxPassword, $shopOldPassword) || !preg_match($regxPassword, $shopNewPassword)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "密码格式错误！"));
        }
        
        if (!!D("Shop")->checkPassword($shopId, sha1(md5($shopOldPassword)))) {
            if (!!D("Shop")->updatePassword($shopId, sha1(md5($shopNewPassword)))) {
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "更新成功！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "更新失败，请检查后重试！"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "原始密码输入错误，请检查后重试！"));
        }
    }
    
    //显示菜单页面
    public function menu() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }
        
        $menuStatus = M("SystemConfig")->where("set_type = 'menu_status' AND set_code <> 0")->order("set_code ASC")->select();
        $menuList = D("Menu")->getShopMenuListByShopId($shopId);
        $this->assign("menuStatus", $menuStatus);
        $this->assign("menuList", $menuList);

        $this->display();
    }
    
    //更改菜单状态
    public function changeMenuStatus() {
        session_start();
        $menuId = decodeMenuId(intval($this->_param(2)));
        $menuStatus = intval($this->_param(3));

        $shopId = session("shopId");
        if (D("Menu")->boolShopIdWithMenuId($shopId, $menuId)) {
            $flag = D("Menu")->changeMenuStatus($shopId, $menuId, $menuStatus);
            
            if ($flag == 1) {
                 D("ShopLogs")->shopLogs(20002, json_encode(array($shopId, $menuId, $menuStatus)));      //记录操作
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "操作成功！"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "操作失败，请刷新后重试！"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您没有该餐单，请刷新后重试！"));
        }
    }
    
     //添加新菜单
    public function addNewMenu() {
        session_start();
        $menuName = $this->_param(2);
        $menuPrice = $this->_param(3);        
        $menuImage = session("new_menu_image");
        
        if (!$menuImage) {
            $menuImage = "";
        }
        
        if (D("Menu")->addNewMenu($menuName, $menuPrice, $menuImage)) {
            session("new_menu_image", null);            //清除图片
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "添加成功！"));
        } else {
             $this->ajaxReturn(array("responce" => "FAILED", "message" => "添加失败，请检查输入是否有误！"));
        }
    }
    
    /*
     *  菜单图片 图片地址是用session("new_menu_image")存着的
     */
    public function newMenuImageUpload() {
        session_start();
        import('ORG.Net.UploadFile'); 
        $shopId = session("shopId");        //店铺的id
        $upload = new UploadFile();   
        $upload->maxSize = 5242880;     //5M     //设置上传文件大小   
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp');      //设置上传文件类型
          
        $upload->savePath = 'data/menu/'.$shopId."/";      //设置附件上传目录 
        $upload->thumb = true;      //设置需要生成缩略图，仅对图像文件有效      
        $upload->imageClassPath = 'ORG.Util.Image';     // 设置引用图片类库包路径 
        $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图    //设置需要生成缩略图的文件后缀     
        $upload->thumbMaxWidth = '400,80';    //设置缩略图最大宽度   
        $upload->thumbMaxHeight = '400,80';          //设置缩略图最大高度 
        $upload->saveRule = uniqid;         //设置上传文件规则     
        $upload->thumbRemoveOrigin = true;      //删除原图  
        if (!$upload->upload()) {   
            //捕获上传异常   
            $this->ajaxReturn(array("responce" => "FAILED", "message" => $upload->getErrorMsg()));
        } else {   
            //取得成功上传的文件信息   
            $uploadList = $upload->getUploadFileInfo();   
            session("new_menu_image", "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']);
             $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']));
        }   
    }
    
    
    //更新的菜单图片
    /*
     *  菜单图片 图片地址是用session("new_menu_image")存着的
     */
    public function menuImageUpdate() {
        session_start();
        import('ORG.Net.UploadFile'); 
        $shopId = session("shopId");        //店铺的id
        $upload = new UploadFile();   
        $upload->maxSize = 5242880;     //5M     //设置上传文件大小   
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp');      //设置上传文件类型
          
        $upload->savePath = 'data/menu/'.$shopId."/";      //设置附件上传目录 
        $upload->thumb = true;      //设置需要生成缩略图，仅对图像文件有效      
        $upload->imageClassPath = 'ORG.Util.Image';     // 设置引用图片类库包路径 
        $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图    //设置需要生成缩略图的文件后缀     
        $upload->thumbMaxWidth = '200,80';    //设置缩略图最大宽度   
        $upload->thumbMaxHeight = '200,80';          //设置缩略图最大高度 
        $upload->saveRule = uniqid;         //设置上传文件规则     
        $upload->thumbRemoveOrigin = true;      //删除原图  
        if (!$upload->upload()) {   
            //捕获上传异常   
            $this->ajaxReturn(array("responce" => "FAILED", "message" => $upload->getErrorMsg()));
        } else {   
            //取得成功上传的文件信息   
            $uploadList = $upload->getUploadFileInfo();   
            session("menu_image_update", "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']);
             $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']));
        }   
    }
    
    //menu的序号修改
    public function menuIndexUpdate() {
        session_start();
        $menuId = decodeMenuId(intval($this->_param(2)));
        $menuIndex = intval($this->_param(3));
        $shopId = session("shopId");
        
        if (D("Menu")->boolShopIdWithMenuId($shopId, $menuId)) {
            $flag = D("Menu")->menuIndexUpdate($menuId, $menuIndex);
            if ($flag == 0 || $flag == 1) {
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "菜单序号更改成功"));
            }
        } else {
             $this->ajaxReturn(array("responce" => "FAILED", "message" => "您没有该菜单，请刷新后重试！"));
        }
    }
    
    //更新菜单信息
    public function updateMenu() {
        session_start();
        $menuId = decodeMenuId(intval($this->_param(2)));
        $shopId = session("shopId");
        $menuName = $this->_param(3);
        $menuPrice = $this->_param(4);
        $menuImage = session("menu_image_update");
        
        
        
        if (D("Menu")->boolShopIdWithMenuId($shopId, $menuId)) {
            $flag = D("Menu")->menuInfoUpdate($menuId, $menuName, $menuPrice, $menuImage);
            if ($flag == 0 || $flag == 1) {
                session("menu_image_update", null);
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "信息更新成功"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您没有该餐单，请刷新后重试！"));
        }        
    }
    
    //顾客列表页面
    public function customer() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }
        
        $orderBy = $this->_param(2);        //排序方法
        if ($orderBy == "amount") {
            //总消费额
            $customerInfo = D("Order")->getCustomInfoByAmount();
        } else if ($orderBy == "count") {
            //总订单数
            $customerInfo = D("Order")->getCustomInfoByCount();
        } else {
            //根据最近的时间
            $customerInfo = D("Order")->getCustomInfoByDate();
        }
        
        $this->assign("customerInfo", $customerInfo);
        $this->display();
    }
    
    //用户的订单详情
    public function detail() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }
        
        $userId = decodeUserId(intval($this->_param(2)));
        $orderList = D("Order")->getUserOrderByUserId($shopId, $userId);
        
        $detailModel = M("Order_detail");;
        //给每个订单添加具体的菜单
        foreach($orderList as $key => $value) {
            $orderId = $value['oid'];
            $orderList[$key]['menuList'] = $detailModel->where("order_id = $orderId")->select();
        }
        
        $this->assign("orderList", $orderList);
        $this->display();
    }
    
      //移动端获取新的订单信息
    public function get_new_menu_from_mobile() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();

        $shopId = session("shopId");
        $orderList = D("Order")->getNewOrderList($shopId);
        $detailModel = M("Order_detail");;
        //给每个订单添加具体的菜单
        foreach($orderList as $key => $value) {
            $orderId = $value['oid'];
            $orderList[$key]['menuList'] = $detailModel->where("order_id = $orderId")->select();
        }

        $checkWhetherHaveNewOrder = D("Order")->checkNewOrder($shopId);

        if ($checkWhetherHaveNewOrder) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "data" => $orderList));
       } else {
            $this->ajaxReturn(array("responce" => "FAILED",  "data" => $orderList));
       }
    }
    
    //店铺信息管理
    public function info() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }
        
        $shopInfo = D("Shop")->getShopDetailByShopId($shopId);
        $this->assign("shopInfo", $shopInfo);
        $this->display();
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
    
    //商家确认订单
    public function acceptOrderByShopUser() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();
        $shopId = session("shopId");
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        $orderAddress = $this->_post("orderAddress");
        $orderPhone = $this->_post("orderPhone");
        $orderUserName = $this->_post("orderUserName");
        $shopSpeed = D("Shop")->getShopSpeed($shopId);
        $orderPrice = $this->_post("orderPrice");
        //$shopPhone = D("Shop")->getShopPhone($shopId);
 
        if (D("Order")->boolOrderWithShop($shopId, $orderId)) {
            
            //确认发送短信
            if ($this->sendMessageToUser($orderPhone, $orderUserName, $orderAddress, $shopSpeed, $orderPrice)) {
                M("order")->where("oid = $orderId")->save(array("order_status" => 3));      //确认订单
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "订单已成功确认！"));
           }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您的餐厅没有该订单，请刷新后重试！"));
        }
    }
    
    //商家拒绝订单
    public function rejectOrderByShopUser() {
        if (isset($_POST["__wx__"])) {
            session_id($this->_post("__wx__"));
        }
        session_start();
        $shopId = session("shopId");
        $orderId = decodeOrderId(intval($this->_post("orderId")));
        $reason = $this->_post("reason");
        $orderPhone = $this->_post("orderPhone");
        $orderUserName = $this->_post("orderUserName");
        
        if (D("Order")->boolOrderWithShop($shopId, $orderId)) {
         //   if ($this->sendRejectMessageToUser($orderPhone, $orderUserName, $reason)) {
                M("order")->where("oid = $orderId")->save(array("order_status" => 4, "reject_reason" => $reason));      //确认订单
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "拒绝成功！"));
           // }
            
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您的餐厅没有该订单，请刷新后重试！"));
        }
    }
    
    //餐厅名字修改
    public function changeShopName() {
        session_start();
        $shopName = $this->_post("shopName");
        $regx = "/^.{1,42}$/i";
        $shopId = session("shopId");
        
        if (!preg_match($regx, $shopName)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您的餐厅名字超出规定长度，请检查您的输入是否有误，有任何疑问请联系客服！"));
        }
        
        if (M("Shop")->where("shop_id = $shopId")->save(array("shop_name" => $shopName))) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "店铺名字修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "店铺名字修改失败，请确认您的输入没有错误！"));
        }
    }
    
    //餐厅Logo上传
    public function shopImageUpload() {
        session_start();
        import('ORG.Net.UploadFile'); 
        $shopId = session("shopId");        //店铺的id
        $upload = new UploadFile();   
        $upload->maxSize = 5242880;     //5M     //设置上传文件大小   
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp');      //设置上传文件类型
          
        $upload->savePath = 'data/shop/'.$shopId."/";      //设置附件上传目录 
        $upload->thumb = true;      //设置需要生成缩略图，仅对图像文件有效      
        $upload->imageClassPath = 'ORG.Util.Image';     // 设置引用图片类库包路径 
        $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图    //设置需要生成缩略图的文件后缀     
        $upload->thumbMaxWidth = '200,80';    //设置缩略图最大宽度   
        $upload->thumbMaxHeight = '200,80';          //设置缩略图最大高度 
        $upload->saveRule = uniqid;         //设置上传文件规则     
        $upload->thumbRemoveOrigin = true;      //删除原图  

        if (!$upload->upload()) {   
            //捕获上传异常   
            $this->ajaxReturn(array("responce" => "FAILED", "message" => $upload->getErrorMsg()));
        } else {   
            //取得成功上传的文件信息   
            $uploadList = $upload->getUploadFileInfo();   
            session("shop_image_tmp", "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']);
             $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "/".$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename']));
        }   
    }
    
    //店铺Logo修改确认
    public function shopImageConfirm() {
        session_start();
        $shopId = session("shopId");
        $imagePath = session("shop_image_tmp");
        
        if ($imagePath) {
            if (M("Shop")->where("shop_id = $shopId")->save(array("shop_image" => $imagePath))) {
                 session("shop_image_tmp", null);
                 $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "店铺Logo修改成功"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "店铺Logo修改失败，请确认你的输入没有错误！"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "您还未上传店铺Logo"));
        }
    }
    
    //餐厅起送价确认
    public function changeShopStartPrice() {
        session_start();
        $startPrice = $this->_post("shopStartPrice");
        $shopId = session("shopId");
        $regx = "/^\d{1,3}(\.)?\d{0,2}$/";
        if (!preg_match($regx, $startPrice)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "起送价格式不符合规范，请修改后重新输入！"));
        }
        
        M("Shop")->where("shop_id = $shopId")->save(array("shop_start_price" => $startPrice));
        $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "店铺起送价格修改成功！"));
      
    }
    
    //餐厅起送价确认
    public function changeShopSpeed() {
        session_start();
        $speed = $this->_post("shopSpeed");
        $shopId = session("shopId");
        $regx = "/^.{0,200}$/";
        if (!preg_match($regx, $speed)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "送餐时间设置的长度不符合规范，请修改后重新输入！"));
        }
        
        M("Shop")->where("shop_id = $shopId")->save(array("shop_speed" => $speed));
        $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "送餐时长修改成功！"));
      
    }
    
    //餐厅订餐电话提交确认
    public function changeShopPhone() {
        session_start();
        $shopPhone = $this->_post("shopPhone");
        $shopId = session("shopId");
        $regx = "/.{0,20}/";
        if (!preg_match($regx, $shopPhone)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "订餐电话不符合规范，请修改后重新输入！"));
        }
        
        if (M("Shop")->where("shop_id = $shopId")->save(array("shop_phone" => $shopPhone))) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "订餐电话修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "订餐电话修改失败，请确认您的输入没有错误！"));
        } 
    }
    
    //餐厅简介提交确认
    public function changeShopIntroduce() {
        session_start();
        $shopIntroduce = $this->_post("shopIntroduce");
        $shopId = session("shopId");
        $regx = "/^.{0,2000}$/";
        if (!preg_match($regx, $shopIntroduce)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "简介长度不符合规范"));
        }
        $flag = M("Shop")->where("shop_id = $shopId")->save(array("shop_introduce" => $shopIntroduce));
        if ($flag == 0 || $flag) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅简介修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "简介修改失败，请确认您的输入没有错误！"));
        } 
    }

    //餐厅通告提交确认
    public function changeShopNotify() {
        session_start();
        $shopNotify = $this->_post("shopNotify");
        $shopId = session("shopId");
        $regx = "/^.{0,2000}$/";
        if (!preg_match($regx, $shopNotify)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "通告长度不符合规范"));
        }
        $flag = M("Shop")->where("shop_id = $shopId")->save(array("shop_notify" => $shopNotify));
        if ($flag == 0 || $flag) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "通告修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "通告修改失败，请确认您的输入没有错误！"));
        } 
    }
    
     //餐厅地址修改提交
    public function changeShopAddress() {
        session_start();
        $shopAddress = $this->_post("shopAddress");
        $shopId = session("shopId");
        $regx = "/^.{0,500}$/";
        if (!preg_match($regx, $shopAddress)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "地址格式不符合规范"));
        }
        $flag = M("Shop")->where("shop_id = $shopId")->save(array("shop_address" => $shopAddress));
        if ($flag == 0 || $flag) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅地址修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "餐厅地址修改失败，请确认您的输入没有错误！"));
        } 
    }
    
      //餐厅营业时间修改提交
    public function changeShopTime() {
        session_start();
        $shopTime = $this->_post("shopTime");
        $shopId = session("shopId");
        $regx = "/^.{0,200}$/";
        if (!preg_match($regx, $shopTime)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "时间格式格式不符合规范"));
        }
        $flag = M("Shop")->where("shop_id = $shopId")->save(array("shop_open_time" => $shopTime));
        if ($flag == 0 || $flag) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅营业时间修改成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "餐厅营业时间修改失败，请确认您的输入没有错误！"));
        } 
    }
    
    //餐厅状态
    public function shopStatus() {
        session_start();
        $status = intval($this->_post("status"));
        $shopId = session("shopId");
        switch ($status){
            case 0: M("shop")->where("shop_id = $shopId")->save(array("shop_status" => 0));break;
            case 1: M("shop")->where("shop_id = $shopId")->save(array("shop_status" => 1));break;
            case 2: M("shop")->where("shop_id = $shopId")->save(array("shop_status" => 2));break;
            case 3: M("shop")->where("shop_id = $shopId")->save(array("shop_status" => 3));break;
            default: $this->ajaxReturn(array("responce" => "FAILED", "message" => "请确认您的操作是否正确！"));
        }
        
        $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "餐厅状态切换成功！"));
    }
    
    public function qrcode() {
        session_start();
        $shopId = session("shopId");
        if (!$shopId) {
            $this->redirect("/shop/login");
        }
        $shopName = D("Shop")->getShopName($shopId);
        
        $this->assign("shopName", $shopName);
        $this->display();
    }
    
    //修改折扣
    public function setDiscount(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        $shopId = session("shopId");
        
        if (D("Menu")->setDiscount($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "折扣设置成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "折扣设置失败，请检查您是否存在该菜单或已设置！"));
        }
    }
    //取消折扣
    public function cancelDiscount(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        

        $shopId = session("shopId");
        if (D("Menu")->cancelDiscount($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "折扣取消成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "折扣取消失败，请检查您是否存在该菜单或已取消！"));
        } 
    }
    
    
    //设置为新菜
    public function setNewMenu(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        $shopId = session("shopId");
        
        if (D("Menu")->setNewMenu($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "新菜设置成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "新菜设置失败，请检查您是否存在该菜单或已设置！"));
        }
    }
    //取消新菜
    public function cancelNewMenu(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        

        $shopId = session("shopId");
        if (D("Menu")->cancelNewMenu($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "新菜取消成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "新菜取消失败，请检查您是否存在该菜单或已取消！"));
        } 
    }
    
    
    //修改招牌菜
    public function setSpecial(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        $shopId = session("shopId");
        
        if (D("Menu")->setSpecial($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "招牌菜设置成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "招牌菜设置失败，请检查您是否存在该菜单或已设置！"));
        }
    }
    //取消招牌菜
    public function cancelSpecial(){
        session_start();
        $menuId = decodeMenuId(intval($this->_post("menuId")));
        $shopId = session("shopId");
        if (D("Menu")->cancelSpecial($shopId, $menuId)) {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "招牌菜取消成功！"));
        } else {
            $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "招牌菜取消失败，请检查您是否存在该菜单或已取消！"));
        } 
    }

    //商家PC端根据orderId获取店铺和该订单的信息
    public function getOrderAndShopInfo() {
        //不验证登录
        $orderId = decodeOrderId(intval($this->_post("orderId")));

        $responce = M()->table("o_order as o, o_shop as s")->where("o.oid = $orderId AND s.shop_id = o.shop_id")->find();
        $responce['order_detail'] = M("order_detail")->where("order_id = $orderId")->select();
        $this->ajaxReturn($responce);
    }
}

?>
