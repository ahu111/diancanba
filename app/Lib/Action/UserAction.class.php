<?php

class UserAction extends Action {
    public function __construct() {
        R("Public/session_start_by_user");
        session_start();
    }
    public function index() {
        
        $uid = session("uid");
        if (!$uid) {
            $this->error("登陆后再访问", "/user/login");
        }
        
        $userInfo = D("User")->getUserInfoByUserId($uid);
        $this->assign("userInfo", $userInfo);
        
         //收藏的餐厅
        $favoriteList = M()->table("o_shop_favorite as f, o_shop as s")->where("f.user_id = $uid AND f.shop_id = s.shop_id AND "
                . "s.shop_status <> 0")->field("f.shop_id as shop_id, s.shop_name as shop_name, s.shop_image as shop_image")->select();
        $this->assign("favoriteList", $favoriteList);
        
        R("Public/recordUserVisited");          //记录
        $this->display();
    }
    
    //用户详情页面
    public function info() {
        $uid = session("uid");
        if (!$uid) {
            $this->error("登陆后再访问", "/user/login");
        } 
        
        $userInfo = D("User")->getUserInfoByUserId($uid);
        $this->assign("userInfo", $userInfo);
        
        R("Public/recordUserVisited");          //记录
        $this->display();
        
    }
    
    public function login() {
        $this->display();
    }
    
    public function register() {
        $this->display();
    }
    
    //用户退出
    public function login_out() {
        session('[destroy]');
        if (!session("uid")) {
            $this->redirect("/user/login");
        } else {
            $this->redirect("/");
        }
    }
    
    //用户登陆函数
    public function userLogin() {
        $userName = $this->_param(2);
        $userPassword = $this->_param(3);
        
        $regxUserName = '/^[_\x{4e00}-\x{9fa5}A-Za-z0-9]{2,14}$/iu';
        $regxPassword = "/^[a-zA-Z0-9_]{6,20}$/i";
        
        if (!preg_match($regxUserName, $userName)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户名中含有非法字符或长度不符合规范, 用户名由2-14位的中文/英文/数字/下划线构成."));
        }
        
        
        if (preg_match($regxPassword, $userPassword)) {
             $flag = D("User")->getUserLogin($userName, sha1(md5($userPassword)));  //密码先sha1再md5
             if ($flag) {
                 session_set_cookie_params(3600 * 24 * 365, "/");
                 session('[regenerate]');
                 session("uid", $flag['uid']);              //用户的id
                 session("name", $flag['name']);              //用户的名字
                 session("password", $flag['password']);            //用户密码
                 
                 $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "登陆成功"));
             } else {
                 $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户不存在或者密码错误"));
             }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "密码格式错误，密码由6-20位英文/数字/下划线构成."));
        }
        
    }
    
    //用户注册函数
    public function userRegister() {
        $userName = $this->_param(2);
        $userPassword = $this->_param(3);
        
        $regxUserName = '/^[_\x{4e00}-\x{9fa5}A-Za-z0-9]{2,14}$/iu';
        $regxPassword = "/^[a-zA-Z0-9_]{6,20}$/i";
        
        if (!preg_match($regxUserName, $userName)) {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户名中含有非法字符或长度不符合规范, 用户名由2-14位的中文/英文/数字/下划线构成."));
        }
        
        if (preg_match($regxPassword, $userPassword)) {
            //验证用户名的唯一性
            if ($this->checkUserNickNameUnique(0, $userName)) {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "该用户名已经存在！"));
            }
            
             $flag = D("User")->getUserRegister($userName, sha1(md5($userPassword)));  //密码先sha1再md5
             if ($flag) {
                 session_set_cookie_params(3600 * 24 * 365, "/");
                 session('[regenerate]');
                 session("uid", $flag['uid']);
                 session("name", $userName);              //用户的名字
                 session("password", sha1(md5($userPassword)));            //用户密码
                 $this->ajaxReturn(array("responce" => "SUCCESS", "message" => "注册成功"));
             } else {
                 $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户名已经存在,请检查后重试！"));
             }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "密码格式错误，密码由6-20位英文/数字/下划线构成."));
        }
        
    }
    
    //更改用户nickname
    public function userNickNameChange() {
        $userName = $this->_param(2);
        $pattern = '/^[_\x{4e00}-\x{9fa5}A-Za-z0-9]{2,14}$/iu';
        $uid = session("uid");
       
        if (preg_match($pattern, $userName)) {
            
            //验证用户名的唯一性
            if ($this->checkUserNickNameUnique($uid, $userName)) {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "该用户名已经存在,请修改后再次提交！"));
            }
            
            $flag = D("User")->changeUserNickName($userName);
            if ($flag == 1 || $flag == 0) {
                $this->ajaxReturn(array("responce" => "SUCCESS", "message" => $userName));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户名修改失败，请确认您的操作是否正确！"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", "message" => "用户名中含有非法字符或长度不符合规范，用户名由2-14位的中文/英文/数字/下划线构成."));
        }
    }
    
    //验证用户名的唯一性
    public function checkUserNickNameUnique($userId, $userName) {
        return M("user")->where("uid <> $userId AND nickname = '$userName'")->find();
    }
    
    //更改用户送货地址/收货人/收货电话
    public function userAddressPhoneNameChange() {
        $userAddress = $this->_param(2);   //用户地址
        $userPhone = $this->_param(3);      //用户电话
        $userName = $this->_param(4);       //收餐人姓名
        
        $regxAddress = "/^.{4,100}$/i";
        $regxPhone = "/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/";
        $regxName = "/^.{1,20}$/i";
        
        if (!preg_match($regxAddress, $userAddress)) {
           $this->ajaxReturn(array("responce" => "FAILED", 'message' => "地址格式不符合规范，检查后重试！"));
        } else if (!preg_match($regxPhone, $userPhone)){
            $this->ajaxReturn(array("responce" => "FAILED", 'message' => "电话格式不符合规范，检查后重试！"));
        } else if (!preg_match($regxName, $userName)){
            $this->ajaxReturn(array("responce" => "FAILED", 'message' => "姓名格式不符合规范，检查后重试！"));
        } else {
            $flag = D("User")->userAddressPhoneNameChange($userAddress, $userPhone, $userName);
            if ($flag == 0 || $flag == 1) {
                $this->ajaxReturn(array("responce" => "SUCCESS"));
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", 'message' => "提交的内容中含有非法字符，检查后重试！"));
            }
        }
    }
    
    //用户地址修改
    public function userAddressChange() {
        $userAddress = $this->_post("userAddress");
        $regxAddress = "/^.{4,100}$/i";
        
        if (preg_match($regxAddress, $userAddress)) {
            $flag = D("User")->changeUserAddress($userAddress);
        
            if ($flag == 1 || $flag == 0) {
                $responce["responce"] = "SUCCESS";
                $responce['message'] = $userAddress;
            } else {
                $responce['responce'] = "FAILED";
                $responce['message'] = "地址格式不符合规范，检查后重试！";
            }
        } else {
            $responce['responce'] = "FAILED";
            $responce['message'] = "地址格式不符合规范，检查后重试！";
        }

        $this->ajaxReturn($responce);
    }
    
    //用户电话修改
    public function userPhoneChange() {
        $userPhone = $this->_post("userPhone");
        $regxPhone = "/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/";
        
        if (preg_match($regxPhone, $userPhone)) {
            $flag = D("User")->changeUserPhone($userPhone);
        
            if ($flag == 1 || $flag == 0) {
                $responce["responce"] = "SUCCESS";
                $responce['message'] = $userPhone;
            } else {
                $responce['responce'] = "FAILED";
                $responce['message'] = "电话格式不符合规范，检查后重试！";
            }
        } else {
            $responce['responce'] = "FAILED";
            $responce['message'] = "电话格式不符合规范，检查后重试！";
        } 
        $this->ajaxReturn($responce);
    }
    
    //收货的用户名字修改
    public function userNameChange() {
        $userName = $this->_post("userName");
        $regxName = "/^.{1,20}$/i";
        
        if (preg_match($regxName, $userName)) {
            $flag = D("User")->changeUserName($userName);
        
            if ($flag == 1 || $flag == 0) {
                $responce["responce"] = "SUCCESS";
                $responce['message'] = $userName;
            } else {
                $responce['responce'] = "FAILED";
                $responce['message'] = "姓名格式不符合规范，检查后重试！";
            }
        } else {
            $responce['responce'] = "FAILED";
            $responce['message'] = "姓名格式不符合规范，检查后重试！";
        }

        $this->ajaxReturn($responce);
    }
    
    
    //用户密码修改
    public function updatePassword() {
        $oldPassword = $this->_param(2);
        $newPassword = $this->_param(3);
        $regxPassword = "/^[a-zA-Z0-9_]{6,16}$/";
        
        if (preg_match($regxPassword, $oldPassword) && preg_match($regxPassword, $newPassword)) {
            if (session("password") == sha1(md5($oldPassword))) {
                $userId = session("uid");
                $flag = D("User")->updatePassword($userId, sha1(md5($newPassword)));
                
                if ($flag == 1 || $flag == 0) {
                    session("password", sha1(md5($newPassword)));
                    $this->ajaxReturn(array("responce" => "SUCCESS", 'message' => "密码修改成功"));
                } else {
                    $this->ajaxReturn(array("responce" => "FAILED", 'message' => "密码修改错误，请检查您的输入是否有误"));
                }
                
            } else {
                $this->ajaxReturn(array("responce" => "FAILED", 'message' => "初始密码错误，请重新输入"));
            }
        } else {
            $this->ajaxReturn(array("responce" => "FAILED", 'message' => "密码格式错误, 建议由6-16位 英文/下划线/数字组成"));
        }
        
    }
    
}

?>
