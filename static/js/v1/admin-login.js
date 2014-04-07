$(function() {
    
    //用户登录函数
   $("#admin-login-btn").bind("click", function() {
       
       var userEmail = $("#inputUserEmail").val(),
           userPassword = $("#inputPassword").val(),
           regxAccount = /^[a-zA-Z0-9_]{6,16}$/i,
           regxPassword = /^[a-zA-Z0-9_-]{6,20}$/i;
           
       //检验最基本的用户名有效性
       //if (regxAccount.test(userEmail) && regxPassword.test(userPassword)) {
           $.post(
                "/admin/adminLogin/",
                {"userEmail": userEmail, "userPassword": userPassword},
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        window.location.href = "/admin/";
                    } else if (responce.responce === "FAILED") {
                        alert(responce.message);
                    }
                },
                "json"
           )
     //  } else {
       //    alert("账号或密码格式错误!");
     //  }
       $("#admin-login-form").submit(false)
   });
   
});

