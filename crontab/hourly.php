<?php
/*
 * 每个小时点、统计order_detail中的评分、计算对应商铺总的平均分、对应菜的总分、评价人数、对应菜购买人数
 */

mysql_connect("localhost", "xiaotao", "Xiaotao2013");  
mysql_select_db("order");
mysql_query("SET NAMES utf8");

//如果发生错误则发送邮件通知
function sendErrorMail($word = "success") {
    mail("lizhug.steven@gmail.com", "diancanba error", "$word", "diancanba");
}

/*
 * //店铺总评分
 * 
 */
$sql = "SELECT SUM(d.grade) as grade_sum, COUNT(d.order_id) as people_sum, o.shop_id FROM o_order_detail as d, o_order as o, o_shop as s "
        . "WHERE d.is_grade = 1 AND o.shop_id = s.shop_id AND o.oid = d.order_id GROUP by o.shop_id";          
$row = mysql_query($sql) or die(sendErrorMail("error 100000"));

while($result = mysql_fetch_array($row)) {
    $shopId = $result['shop_id'];
    $peopleGrade = $result['people_sum'];           //评价的人数、其实是已经评价过的订单的数量
    $averageGrade = number_format($result['grade_sum'] / $result['people_sum'], 1);
    $sql = "UPDATE o_shop SET shop_average_grade = '$averageGrade', shop_people_grade = '$peopleGrade' WHERE shop_id = '$shopId'";
    mysql_query($sql) or die(sendErrorMail("error 100001"));
}


/*
 * 每个店铺的菜单评价
 * 
 */
$sql = "SELECT SUM(d.grade) as grade_sum, COUNT(d.menu_id) as people_sum, d.menu_id FROM o_order_detail as d "
        . "WHERE d.is_grade = 1 GROUP by d.menu_id";          
$row = mysql_query($sql) or die(sendErrorMail("error 100002"));

while($result = mysql_fetch_array($row)) {
    $menuId = $result['menu_id'];
    $peopleGrade = $result['people_sum'];           //评价的人数、其实是已经评价过的订单的数量
    $averageGrade = number_format($result['grade_sum'] / $result['people_sum'], 1);
    $sql = "UPDATE o_menu SET remark_ave_grade = '$averageGrade', remark_number = '$peopleGrade' WHERE menu_id = '$menuId'";
    mysql_query($sql) or die(sendErrorMail("error 100003"));
}

//每个菜单每月售出的数量
$sql = "SELECT COUNT(d.menu_id) as sold_per_month, d.menu_id FROM o_order_detail as d, o_order as o"
        . " WHERE d.order_id = o.oid AND o.order_status = 1 AND  to_days(NOW()) - to_days(o.date) < 31 GROUP by d.menu_id";          
$row = mysql_query($sql) or die(sendErrorMail("error 100004"));

while($result = mysql_fetch_array($row)) {
    $menuId = $result['menu_id'];
    $soldPerMonth = $result['sold_per_month'];
    
    $sql = "UPDATE o_menu SET sold_per_month = '$soldPerMonth' WHERE menu_id = '$menuId'";
    mysql_query($sql) or die(sendErrorMail("error 100005"));
}

?>