<?php

//计算餐厅的访问量  每月的 每周的  每天的

mysql_connect("localhost", "xiaotao", "Xiaotao2013");  
mysql_select_db("order");
mysql_query("SET NAMES utf8");

//如果发生错误则发送邮件通知
function sendErrorMail($word = "success") {
    mail("lizhug.steven@gmail.com", "diancanba error", "$word", "diancanba");
}

//每月
$sql = "SELECT COUNT(*) as sum_month, shop_id FROM o_shop_uv_logs  WHERE to_days(now()) - to_days(date) < 31 GROUP BY shop_id";
$row = mysql_query($sql) or die(sendErrorMail("error 100006"));

while($result = mysql_fetch_array($row)) {
    $shopId = $result['shop_id'];
    $sumMonth = $result['sum_month'];
    
    $sql = "UPDATE o_shop SET shop_visit_month = '$sumMonth' WHERE shop_id = '$shopId'";
    mysql_query($sql) or die(sendErrorMail("error 100007"));
}

//每周
$sql = "SELECT COUNT(*) as sum_week, shop_id FROM o_shop_uv_logs  WHERE to_days(now()) - to_days(date) < 7 GROUP BY shop_id";
$row = mysql_query($sql) or die(sendErrorMail("error 100008"));

while($result = mysql_fetch_array($row)) {
    $shopId = $result['shop_id'];
    $sumMonth = $result['sum_week'];
    
    $sql = "UPDATE o_shop SET shop_visit_week = '$sumMonth' WHERE shop_id = '$shopId'";
    mysql_query($sql) or die(sendErrorMail("error 100009"));
}

//每周
$sql = "SELECT COUNT(*) as sum_day, shop_id FROM o_shop_uv_logs  WHERE to_days(now()) - to_days(date) < 7 GROUP BY shop_id";
$row = mysql_query($sql) or die(sendErrorMail("error 100010"));

while($result = mysql_fetch_array($row)) {
    $shopId = $result['shop_id'];
    $sumMonth = $result['sum_day'];
    
    $sql = "UPDATE o_shop SET shop_visit_day = '$sumMonth' WHERE shop_id = '$shopId'";
    mysql_query($sql) or die(sendErrorMail("error 100011"));
}


//确认用户未确认的订单
$sql = "UPDATE o_order SET order_status = 1 WHERE order_status = 3 AND to_days(now()) - to_days(date) <= 1";
mysql_query($sql) or die(sendErrorMail("error 100012"));