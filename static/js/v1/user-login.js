$(function() {
    
    //用户登录函数
   $("#user-login-btn").bind("click", function() {
       
       var userName = $("#inputUserName").val(),
           userPassword = $("#inputPassword").val(),
           regxString = /^[a-zA-Z0-9_]{6,20}$/i,
           regxUserName = /^[_\u4E00-\u9FFFA-Za-z0-9]{2,14}$/i;
       //检验最基本的用户名有效性
       if (!regxUserName.test(userName)) {
           alert("用户名中含有非法字符或长度不符合规范, 用户名由2-14位的中文/英文/数字/下划线构成.");
           $("#user-login-form").submit(false);
           return;
       }
       
       if (regxString.test(userPassword)) {
           $.post(
                "/user/userLogin/" + userName + "/" + userPassword,
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
       $("#user-login-form").submit(false);
   });
   
});

