$(function() {
    
    //用户登录函数
   $("#shop-login-btn").bind("click", function() {
       
       var userEmail = $("#inputUserEmail").val(),
           userPassword = $("#inputPassword").val(),
           regxEmail = /^[a-zA-Z0-9-._]{1,50}@[a-zA-Z0-9-]{1,65}.(com|net|org|info|biz|([a-z]{2,3}.[a-z]{2}))$/i,
           regxPassword = /^[a-zA-Z0-9_]{6,20}$/i;
           
       //检验最基本的用户名有效性
       if (regxEmail.test(userEmail) && regxPassword.test(userPassword)) {
           $.post(
                "/shop/shopLogin/",
                {"userEmail": userEmail, "userPassword": userPassword},
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        window.location.href = "/shop/";
                    } else if (responce.responce === "FAILED") {
                        alert(responce.message);
                    }
                },
                "json"
           )
       } else {
           alert("邮箱/手机或密码格式错误!");
       }
       $("#shop-login-form").submit(false)
   });
   
});

