$(function() {
   
   //用户名修改
   $("#user-name-change").click(function() {
       var flag = confirm("只有一次更改用户名的机会，是否继续？");
       if (flag) {
           $('#user-name-text').hide();
           $("#user-name-change").hide();
           $("#user-name-input").show().focus();
           $("#user-name-submit-btn").bind("click", userNameChangeSubmit).show();
           $(".menu-tab").hide();
       }
   });
   
   //用户名修改提交函数
   function userNameChangeSubmit() {
       var userName = $.trim($("#user-name-input").val()),
           regxString = /^[_\u4E00-\u9FFFA-Za-z0-9]{2,14}$/i,
           userNameOrigin = $.trim($("#user-name-text").text());
        
        //相同的时候就不发ajax请求了
       if (userName === userNameOrigin) {
           $("#user-name-input").hide();
            $("#user-name-text").show();
            $("#user-name-submit-btn").unbind("click").hide();
            $("#user-name-change").show();
            $(".menu-tab").show();
            return ;
       }
   
        if (regxString.test(userName)) {
            $.post(
                "/user/userNickNameChange/" + userName,
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        $("#user-name-input").remove();
                        $("#user-name-text").text(responce.message).show();
                        $("#user-name-submit-btn").remove();
                        $("#user-name-change").remove();
                        $(".menu-tab").show();
                    } else if (responce.responce === "FAILED"){
                        alert(responce.message);
                    }
                },
                "json"
            );
        } else {
            alert("用户名中含有非法字符或长度不符合规范，用户名由2-14位的中文/英文/数字/下划线构成.");
        }
   }
   
   //修改送餐信息函数
   $("#user-address-change-btn").bind("click", addressInfoAppear);
   
   //送餐修改显示函数
   function addressInfoAppear() {
      $(".list-info-text").hide();
      $(".list-title-wrap input").show();
      $("#user-address-change-btn").hide();
      $("#user-address-submit").bind("click", addressInfoSubmit).show();
      $(".menu-tab").hide();        //隐藏菜单栏
   }
   
   //送餐信息提交
   function addressInfoSubmit() {
       var userAddress = $.trim($(".user-address-input").val()),
           regxAddress = /^.{4,100}$/i,
           userPhone = $.trim($(".user-phone-input").val()),
           regxPhone = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/,
           userName = $.trim($(".user-order-name-input").val()),
           regxName = /^.{1,20}$/i;
           
       if (!regxAddress.test(userAddress)) {
           alert("地址格式不符合规范，检查后重试！");
           return ;
       } else if (!regxPhone.test(userPhone)){
           alert("电话格式不符合规范，检查后重试！");
           return ;
       } else if (!regxName.test(userName)){
           alert("名字格式不符合规范，检查后重试！");
           return ;
       } else {

           $.post(
                "/user/userAddressPhoneNameChange/" + userAddress + "/" + userPhone + "/" + userName,
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        $(".user-address-input").val(userAddress).hide();
                        $(".user-phone-input").val(userPhone).hide();
                        $(".user-order-name-input").val(userName).hide();
                        
                        $(".user-address-text").text(userAddress).show();
                        $(".user-phone-text").text(userPhone).show();
                        $(".user-name-text").text(userName).show();
                        $("#user-address-submit").unbind("click").hide();
                        $("#user-address-change-btn").show();
                        $(".menu-tab").show();          //显示菜单栏
                    } else if (responce.responce === "FAILED") {
                        alert(responce.message);
                    }
                },
                "json"
           )
       }
   }
   
   $("#user-address-add").bind("click", addressInfoAdd);
   
   //用户地址等信息提交
   function addressInfoAdd() {
       $(".info-add-wrap, .info-add-wrap input").show();         //显示填写的区域
       $(".alert-user-address").remove();           //移除通知
       $("#user-address-submit").bind("click", addressInfoSubmit).show();
       $("#user-address-add").unbind("click").text("送餐信息");
       $(".menu-tab").hide();
   }
   
   
   //显示修改框
   $("#user-password-change").bind("click", function() {
       $(".box-password-change").show(500);
       $("#box-password-change-submit").bind("click", passwordSubmit);
       $(".menu-tab").hide();
   })
   
   $("#box-password-change-cancel").bind("click", function(){
        $("#box-password-change-submit").unbind("click");
        $(".box-password-change").hide(400);
        $(".box-password-change input").val("");
        $(".menu-tab").show();
   })
   
   //密码提交函数
   function passwordSubmit(){
       var oldPassword = $("#box-old-password").val(),
           newPassword = $("#box-new-password").val(),
           regxPassword = /^[a-zA-Z0-9_]{6,16}$/;
   
           if (regxPassword.test(oldPassword) && regxPassword.test(newPassword)) {
               $.post(
                       "/user/updatePassword/" + oldPassword + "/" + newPassword,
                       function(responce) {
                           if (responce.responce === "SUCCESS") {
                               alert(responce.message);
                               $("#box-password-change-cancel").trigger("click");
                           }else {
                               alert(responce.message);
                           }
                       },
                       "json"
               );
           } else {
               alert("密码格式错误, 建议由6-16位 英文/下划线/数字组成");
           }
   }
});