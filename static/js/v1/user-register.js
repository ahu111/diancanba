$(function() {
    
    //用户注册函数
   $("#user-register-btn").bind("click", function() {
       
       var userName = $.trim($("#inputUserName").val()),
           userPassword = $.trim($("#inputPassword").val()),
           userPasswordRepeat = $.trim($("#inputPasswordRepeat").val()),
           regxUserName = /^[_\u4E00-\u9FFFA-Za-z0-9]{2,14}$/i,
           regxPassword = /^[a-zA-Z0-9_]{6,16}$/;
       
       //检查密码重复性
       if (userPassword !== userPasswordRepeat) {
           alert("两次密码不一致！");
           $("#user-register-form").submit(false);
           return ;
       }
       
       //检验最基本的用户名有效性
       if (!regxUserName.test(userName)) {
            alert("用户名中含有非法字符或长度不符合规范, 用户名由2-14位的中文/英文/数字/下划线构成.");
            $("#user-register-form").submit(false);
            return;
       }
       
       if (regxPassword.test(userPassword)) {
           $.post(
                "/user/userRegister/" + userName + "/" + userPassword,
                function(responce) {
                  if (responce.responce === "SUCCESS") {
                      window.location.href = "/";
                  } else if (responce.responce === "FAILED") {
                      alert(responce.message);
                  }
                },
                "json"
           )
       } else {
          alert("密码格式错误，密码由6-20位英文/数字/下划线构成.");
       }
       $("#user-register-form").submit(false);
   });
   
});

