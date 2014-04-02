$(function(){
    
    //如果有该店铺的未完成的订单，订单将会呈现在列表上
    function initIfOrderExsit() {
        var shopIndex = $("#shopIndex").val();
        $.post(
            "/rest/initOrder",
            {"shopIndex": shopIndex},
            function(responce) {
                if (responce.responce == "SUCCESS") {
                    var orderInfo = responce.orderInfo,
                        menuNumber = $(".menu-number");
                        
                    $("#shop-cart-price-total").text("￥" + orderInfo.totalPrice);   //更新总价
                    $("#shop-cart-number").text(orderInfo.totalNumber + " 份");  //更新份数

                    //总价钱 超过 起送价
                    if (orderInfo.totalPrice - orderInfo.shopStartPrice >= 0) {
                        $("#orderCheck").removeClass("btn-primary")
                           .addClass("btn-success")
                           .text("提交订单")
                           .bind("click", orderCheck);
                    } else {
                        $("#orderCheck").removeClass("btn-success")
                           .addClass("btn-primary")
                           .text("还差" + (orderInfo.shopStartPrice - orderInfo.totalPrice) + "元起送")
                           .unbind("click", orderCheck);
                    }
                    
                    //初始化数量
                    for (var i = 0;; ++i) {
                        
                        if (orderInfo[i] != undefined) {
                            
                            if (orderInfo[i]['menuId'] != undefined && orderInfo[i]['menuId'] != null) {
                                var lengthMenu = menuNumber.length;
                                for (var j = 0; j < lengthMenu; ++j) {
                                    if (menuNumber.eq(j).attr("menuIndex") == orderInfo[i]['menuId']) {
                                        menuNumber.eq(j).val(orderInfo[i]['menuCount']);
                                        break;
                                    }
                                }
                            }
                        } else {
                            break;
                        }                        
                    }
                }
            },
            "json"
        )
    }
        
    //提交订单的函数
    function orderCheck() {
        window.location.href = "/rest/order";            
    }
    
    //订单数量改变
    $(".menu-minus, .menu-plus").bind("click", function(){
        var menuNumber = $(this).parent().find(".menu-number");
            
        //当能够把数量减1的时候
        var menuIndex = menuNumber.attr("menuIndex"),
            shopIndex = $("#shopIndex").val(),
            operator = $(this).hasClass("menu-plus") ? "PLUS" : "MINUS";

        $.post(
            "/rest/orderChange",
            {
                "menuIndex": menuIndex,
                "operator": operator,
                "shopIndex": shopIndex
            },
            function(responce) {
                if (responce.responce == "FAILED") {
                    alert(responce.message);
                } else if (responce.responce == "SUCCESS"){
                    var orderInfo = responce.orderInfo;
                     menuNumber.val(responce.responceNumber);    //数量变为正常的数量
                     $("#shop-cart-price-total").text("￥" + orderInfo.totalPrice);   //更新总价
                     $("#shop-cart-number").text(orderInfo.totalNumber + " 份");  //更新份数
                     
                     //总价钱 超过 起送价
                     if (orderInfo.totalPrice - orderInfo.shopStartPrice >= 0) {
                         $("#orderCheck").removeClass("btn-primary")
                            .addClass("btn-success")
                            .text("提交订单")
                            .bind("click", orderCheck);
                     } else {
                         $("#orderCheck").removeClass("btn-success")
                            .addClass("btn-primary")
                            .text("还差" + (orderInfo.shopStartPrice - orderInfo.totalPrice) + "元起送")
                            .unbind("click", orderCheck);
                     }
                }
            },
            "json"
        )
    });
    
    //收藏店铺
    $(".favorite-shop").live("click", function(){
        var shopIndex = $("#shopIndex").val();
        $.post(
            "/rest/favorite/" + shopIndex,
            function(responce) {
                if (responce.responce === "SUCCESS") {
                    alert(responce.message);
                    window.location.reload();
                } else {
                    alert(responce.message);
                }
            },
            "json"
        )
    });
    
    //取消收藏店铺
    $(".favorite-shop-done").live("click", function(){
        var shopIndex = $("#shopIndex").val();
        $.post(
            "/rest/favorite_remove/" + shopIndex,
            function(responce) {
                if (responce.responce === "SUCCESS") {
                    alert(responce.message);
                    window.location.reload();
                } else {
                    alert(responce.message);
                }
            },
            "json"
        )
    });
    
    initIfOrderExsit();
});
