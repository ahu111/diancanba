<?php

class UserModel extends Model {
    
    //获取用户信息
    public function getUserInfoByUserId($userId) {
        return $this->where("uid = $userId")->limit(1)->find();
    }
    
    //用户地址改变
    public function changeUserAddress($address) {
        $userId = session("uid");
        return $this->where("uid = $userId")->save(array("address" => $address));
    }
    
    //用户电话改变
    public function changeUserPhone($phone) {
        $userId = session("uid");
        return $this->where("uid = $userId")->save(array("phone" => $phone));
    }
    
    //收餐的用户改变
    public function changeUserName($name) {
        $userId = session("uid");
        return $this->where("uid = $userId")->save(array("user_name" => $name));
    }
    
    //用户名修改
    public function changeUserNickName($userNickName) {
        $uid = session("uid");
        return $this->where("uid = $uid AND is_name_change = 0")->save(array("nickname" => $userNickName, "is_name_change" => 1));
    }
    
    //用户登录验证
    public function getUserLogin($userName, $userPasswordEncoded) {
        return $this->where("'$userName' = nickname AND '$userPasswordEncoded' = password")->find();
    }
    
    //用户注册
    public function getUserRegister($userName, $userPasswordEncoded) {
        if ($this->where("nickname = '$userName'")->find()) {
            return false;
        } else {
            $flag = $this->add(array("nickname" => $userName, "password" => $userPasswordEncoded, "is_name_change" => 0, "user_credit" => 0));
            if ($flag){
                return array("nickname" => $userName, "uid" => $flag);
            }
        }
        
    }
    
    //更改用户收货信息
    public function userAddressPhoneNameChange($userAddress, $userPhone, $userName) {
        $uid = session("uid");
        return $this->where("uid = $uid")->save(array("address" => $userAddress, "phone" => $userPhone, "user_name" => $userName));
    }
    
    //更改用户密码
    public function updatePassword($userId, $newPasswordEncoded) {
        return $this->where("uid = $userId")->save(array("password" => $newPasswordEncoded));
    }
    
    public function addUserEvaluateCredit($orderId, $menuId) {
        $credit = intval(rand(0, 5));           //0到5之间取一个值
        $uid = session("uid");
        D("UserLogs")->userLogs(10001, json_encode(array("credit" => $credit, "order_id" => $orderId, "menu_id" => $menuId)));
        $this->where("uid = $uid")->setInc("user_credit", $credit);
    }
    
    
}
?>
