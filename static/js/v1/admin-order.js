$(function(){
    //接受订单
   $(".order-accept").click(function() {
       if (confirm("确认订单？")) {
           var orderId = $(this).parent().find(".order-id").val();
           $.post(
             "/admin/acceptOrderByAdmin/",
             {"orderId": orderId},
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
    
    //是否有新的订单、通过轮训
   (function(){
       var count = 0,
           timeInterval;
       var notifyTime = setInterval(function(){
           $.post(
               "/admin/checkNewOrderByAdmin/",
               function(responce) {
                   if (responce.responce === "SUCCESS") {                   
                        $("#shop-global-alert").text(responce.message).show();  
                        var interval = setInterval(function(){
                            count++;
                             if (count <= 8) {
                                    var video = document.getElementById("notify-voice");
                                       video.play();
                               } else {
                                   count = 0;
                                   clearInterval(interval);
                               }
                        }, 1000);
                   } 
               },
               "json"
           )
       }, 30000);
   }());
});

