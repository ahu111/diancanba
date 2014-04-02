$(function() {
   //接受订单
   $(".order-accept").click(function() {
       if (confirm("确认订单？")) {
           var orderId = $(this).parents(".table-order-detail").find(".order-id").val(),
               orderPhone = $(this).parents(".table-order-detail").find(".order-phone").text(),
               orderAddress = $(this).parents(".table-order-detail").find(".order-address").text(),
               orderUserName = $(this).parents(".table-order-detail").find(".order-user-name").text(),
               orderPrice = $(this).parents(".table-order-detail").find(".order-price").text();
           
          // $("iframe").src("/shop/print").show();
           
           
           $.post(
             "/shop/acceptOrderByShopUser/",
             {"orderId": orderId, "orderPhone": orderPhone, "orderAddress": orderAddress, "orderUserName": orderUserName, "orderPrice": orderPrice},
             function(responce) {
                if (responce.responce === "SUCCESS") {
                    alert(responce.message);
                   // window.location.reload();
                } else {
                    alert(responce.message);
                }
             },
             "json" 
           );
       }
   });
   
   
   //拒绝订单
   $(".order-reject").click(function() {
       var reason = prompt("请输入拒绝理由:");
       if (reason) {
           var orderId = $(this).parent().find(".order-id").val(),
               orderPhone = $(this).parents(".table-order-detail").find(".order-phone").text(),
               orderUserName = $(this).parents(".table-order-detail").find(".order-user-name").text();
               
           $.post(
             "/shop/rejectOrderByShopUser/",
            {"orderId": orderId, "orderPhone": orderPhone, "orderUserName": orderUserName, "reason": reason},
             function(responce) {
                if (responce.responce === "SUCCESS") {
                    alert(responce.message);
                    window.location.reload();
                } else {
                    alert(responce.message);
                }
             },
             "json" 
           );
       }
   });
   
   //店铺查询是否有新的订单、通过轮训
   (function(){
       setInterval(function(){
           $.post(
               "/shop/checkNewOrder/",
               function(responce) {
                   if (responce.responce === "SUCCESS") {                   
                        $("#shop-global-alert").text(responce.message).show(); 
                        $("embed").remove();
                        $("<embed src='/static/voice/new_order_notify.mp3' width=0 height=0 loop='false'>").appendTo($("body"));
                   } 
               },
               "json"
           )
       }, 30000);
       
   }());
});


