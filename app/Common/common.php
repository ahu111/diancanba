<?php

//输出的时候加密订单在数据库中的orderid
function encodeOrderId($orderId) {
    $orderId += 1984;
    return $orderId;
}

//查询的时候加密订单在数据库中的orderid
function decodeOrderId($encodedOrderId) {
    $encodedOrderId -= 1984;
    return $encodedOrderId;
}

//输出的时候加密订单在数据库中的shopid
function encodeShopId($encodedShopId) {
    $encodedShopId += 1620;
    return $encodedShopId;
}

//查询的时候加密订单在数据库中的shopid
function decodeShopId($decodedShopId) {
    $decodedShopId -= 1620;
    return $decodedShopId;
}

//输出的时候加密订单在数据库中的menuid
function encodeMenuId($encodedMenuId) {
    $encodedMenuId += 2038;
    return $encodedMenuId;
}

//查询的时候加密订单在数据库中的menuid
function decodeMenuId($decodedMenuId) {
    $decodedMenuId -= 2038;
    return $decodedMenuId;
}

//输出的时候加密订单在数据库中的userid
function encodeUserId($encodedUserId) {
    $encodedUserId += 2123;
    return $encodedUserId;
}

//查询的时候加密订单在数据库中的userid
function decodeUserId($decodedUserId) {
    $decodedUserId -= 2123;
    return $decodedUserId;
}

//获得分钟间隔
function getMinuteInterval($date) {
    return intval((time() - strtotime($date)) / 60);
}

//获取地点名字
function getAreaName($areaId) {
    return M("Area")->where("area_id = $areaId")->getField("area_name");
}

?>
