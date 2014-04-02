<?php

class MenuModel extends Model {
    
    /*根据店铺id获取店铺的菜单列表
     * 列出条件 1. 未删除 2.不隐藏
     */
    public function getMenuListByShopId($shopId) {
        return $this->where("shop_id = $shopId AND menu_status <> 0 AND menu_status <> 3")->order("menu_index ASC")->select();
    }
    
    
    /*
     * 查询菜谱和店铺是否对应
     */
    public function boolShopIdWithMenuId($shopId, $menuId){
        return $this->where("menu_id = $menuId AND shop_id = $shopId")->find();
    }
    
    /*
     * 根据menu_id获取菜信息
     */
    public function getMenuInfoByMenuId($menuId) {
        return  $this->where("menu_id = $menuId")->limit(1)->find();
    }
    
    
    /*
     * 商户专用函数
     */
   //删除菜单
    public function changeMenuStatus($shopId, $menuId, $menuStatus) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("menu_status" => $menuStatus));
    }
    
    //获取自己的菜单列表
    public function getShopMenuListByShopId($shopId) {
        return $this->where("shop_id = $shopId AND menu_status <> 0")->order("menu_index ASC")->select();
    }
    
    //添加菜单
    public function addNewMenu($menuName, $menuPrice, $menuImage) {
        $shopId = session("shopId");
        return $this->add(array("shop_id" => $shopId, "menu_status" => 1,
            "menu_name" => $menuName, "price" => $menuPrice, "discount" => 0,
            "menu_index" => 100, "menu_image" => $menuImage));
    }
    
    //更改menu_index
    public function menuIndexUpdate($menuId, $menuIndex){
        $shopId = session("shopId");
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("menu_index" => $menuIndex));
    }
    
    //菜单信息更改
    public function menuInfoUpdate($menuId, $menuName, $menuPrice, $menuImage){
        if ($menuImage) {
            return $this->where("menu_id = $menuId")->save(array("menu_name" => $menuName, "price" => $menuPrice,
                "menu_image" => $menuImage));
        } else {
            return $this->where("menu_id = $menuId")->save(array("menu_name" => $menuName, "price" => $menuPrice));
        }
    }
    
    //获得菜单状态
    public function getMenuStatus($menuId) {
        return $this->where("menu_id = $menuId")->getField("menu_status");
    } 
    
    //设置折扣
    public function setDiscount($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("discounted" => 1));
    }
    
    //取消折扣
    public function cancelDiscount($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("discounted" => 0));
    }
    
     //设置为新菜
    public function setNewMenu($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("new_menu" => 1));
    }
    
    //取消新菜
    public function cancelNewMenu($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("new_menu" => 0));
    }
    
     //设置特价菜
    public function setSpecial($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("special" => 1));
    }
    
    //取消特价菜
    public function cancelSpecial($shopId, $menuId) {
        return $this->where("shop_id = $shopId AND menu_id = $menuId")->save(array("special" => 0));
    }
 
}

?>
