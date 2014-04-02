$(function() {
   
    //更新商户信息
   $("#update-shop-info").bind("click", function() {
       var shopUserName = $("#shop-user-name").val(),
           shopUserPhone = $("#shop-user-phone").val(),
           shopSales = $("#shop-sales").val();
           
        $.post(
             "/shop/shopUserInfoUpdate/" + shopUserName + "/" + shopUserPhone + "/" + shopSales,
             function(responce) {
                 if (responce.responce === "SUCCESS") {
                     $("#shop-global-alert").text(responce.message).show(600).delay(3000).hide(600);
                     window.location.reload();
                 } else {
                     $("#shop-global-alert").text(responce.message).show(600).delay(3000).hide(600);
                 }
             },
             "json"
        );

        $("#shop-user-form").submit(false);
   });
   
   $("#shop-password-change").bind("click", showPasswordChange);
   
   //密码修改显示
   function showPasswordChange() {
       $("#shop-password-change").text("取消更改").unbind("click", showPasswordChange).bind("click", cancelPasswordChange);
       $(".password-change-wrap").show();
       $("#password-change-submit").bind("click", passwordChangeSubmit);
   }
   
   function cancelPasswordChange() {
       $("#shop-password-change").text("更改密码").unbind("click", cancelPasswordChange).bind("click", showPasswordChange);
       $(".password-change-wrap").hide();
       $("#password-change-submit").unbind("click");
   }
   
   function passwordChangeSubmit() {
       var shopPassword = $.trim($("#shop-password").val()),
           shopNewPassword = $.trim($("#shop-new-password").val()),
           regxPassword = /^[a-zA-Z0-9_]{6,16}$/;
       if (regxPassword.test(shopPassword) && regxPassword.test(shopNewPassword)) {
           $.post(
                   "/shop/updateShopPassword/" + shopPassword + "/" + shopNewPassword,
                   function(responce) {
                       if (responce.responce === "SUCCESS"){
                           window.location.reload();
                       } else {
                           $("#shop-global-alert").text(responce.message).show(600).delay(3000).hide(600);
                       }
                   },
                   "json"
           );
       } else {
           $("#shop-global-alert").text("您的密码不符合规范,请修改后重试").show(600).delay(3000).hide(600);
       }
   }
   
   //店铺查询是否有新的订单、通过轮训
   (function(){
       var count = 0;
       setInterval(function(){
           $.post(
               "/shop/checkNewOrder/",
               function(responce) {
                   if (responce.responce === "SUCCESS") {                   
                        $("#shop-global-alert").text(responce.message).show();
                        $("embed").remove();
                        $("<embed src='/static/voice/new_order_notify.wav' width=0 height=0 loop='false'>").appendTo($("body"));
                   } 
               },
               "json"
           )
       }, 30000);
       
   }());
});

