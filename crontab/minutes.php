<?php

mysql_connect("localhost", "xiaotao", "Xiaotao2013");  
mysql_select_db("order");
mysql_query("SET NAMES utf8");

//如果发生错误则发送邮件通知
function sendErrorMail($word = "success") {
    mail("lizhug.steven@gmail.com", "diancanba error", "$word", "diancanba");
}


//设置店铺关闭
$sql = "SELECT shop_id  FROM o_shop WHERE scan_time < NOW() - interval 5 minute AND shop_status = 1";
$row = mysql_query($sql) or die(sendErrorMail("error 100016"));

while($result = mysql_fetch_array($row)) {
    $shopId = $result['shop_id'];
    mysql_query("UPDATE o_shop SET shop_status = 2 WHERE shop_id = $shopId") or die(sendErrorMail("error 100015"));
}