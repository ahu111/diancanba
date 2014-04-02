$(function(){
   
   //订单取消
   $("#order-cancel").bind("click", function(){
      var userResponce = prompt("确定取消订单么? 请输入取消的理由");
      if (userResponce != null) {
          var orderIndex = $(this).parents(".order-status").find(".order-index").val();
          $.post(
            "/order/orderCancel/",
             {"orderIndex": orderIndex, "userResponce":userResponce},
             function(responce) {
                 if (responce.code) {
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
   
   
   $("#confirm-order").live("click", function() {
       var userResponce = confirm("是否确认订单?");
      if (userResponce) {
          var orderIndex = $(this).parents(".order-status").find(".order-index").val();
          $.post(
            "/order/orderConfirm/",
            {"orderIndex": orderIndex},
             function(responce) {
                 if (responce.code) {
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
  
});

