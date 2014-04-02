$(function(){
   $("#shop-add-btn").click(function(){
       var email = $("#shop-email").val(),
            regxMail = /^[a-zA-Z0-9-._]{1,50}@[a-zA-Z0-9-]{1,65}.(com|net|org|info|biz|([a-z]{2,3}.[a-z]{2}))$/i;
            
      if (regxMail.test(email)) {
          
          $.post(
            "/admin/addShop/",
            {"email": email},
            function(msg) {
                if (msg.responce == "SUCCESS") {
                    alert(msg.message);
                    window.location.reload();
                } else {
                    alert(msg.message);
                }
            },
            "json"        
          )
      } else {
          alert("邮箱格式错误，请检查！");
      }
      
      $("#form-add-new-shop").submit(false);
   });
});

