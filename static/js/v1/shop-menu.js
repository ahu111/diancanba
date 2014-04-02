$(function() {
   $(".menu-status-select").bind("change", changeMenuStatus);
   
   //删除菜单
   function changeMenuStatus() {
       var $this = $(this),
           menuStatus = $.trim($this.val()),
           menuId = $.trim($this.parents(".menu-list-item").find(".menu_id").val());

       if (menuStatus == 0) {
           if (confirm("确定删除么？")) {
                $.post(
                  "/shop/changeMenuStatus/" + menuId + "/" + menuStatus,
                  function(responce) {
                       if (responce.responce == "SUCCESS") {
                           $this.parents(".menu-list-item").remove();
                            $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                            
                       } else {
                           $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                       }
                  },
                  "json"
                );
            }
       } else {
                $.post(
                  "/shop/changeMenuStatus/" + menuId + "/" + menuStatus,
                  function(responce) {
                       if (responce.responce == "SUCCESS") {
                           $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                       } else {
                           $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                       }
                  },
                  "json"
                );
        } 
    }
    
    //添加新菜单
   $("#add-new-menu-btn").bind("click", function() {
       var menuName = $.trim($("#new-menu-name").val()),
           menuPrice = $.trim($("#new-menu-price").val());
          
          if (menuName == "" || menuPrice == "") {
              $("#shop-global-alert").text("菜名或价格输入不能为空，请重新输入！").show(500).delay(2000).hide(500);
          } else {
              $.post(
                "/shop/addNewMenu/" + menuName + "/" + menuPrice,
                function(responce) {
                      if (responce.responce == "SUCCESS") {
                           $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500, function() {
                               window.location.reload();
                           });
                           
                      } else {
                          $("#shop-global-alert").text(responce.message).show(600).delay(3000).hide(600);
                      }
                },
                "json"
              )
          }
   });
   
   
   //新菜单添加图片 ajax上传        //图片改变就上传
   $("#new-menu-image").live("change", function(){
        $.ajaxFileUpload({  
            url:'/shop/newMenuImageUpload/',            //需要链接到服务器地址  
            secureuri:false,  
            fileElementId:'new-menu-image',                        //文件选择框的id属性  
            dataType: 'json',                                     //服务器返回的格式，可以是json  
            success: function (data, status){             //相当于java中try语句块的用法      
                if (data.responce == "SUCCESS") {
                     $("#new-image-preview").attr("src", data.message).show();
                     $("#shop-global-alert").text("图片上传成功！").show(500).delay(2000).hide(500);
                } else {
                    $("#shop-global-alert").text(data.message).show(500).delay(2000).hide(500);
                }
               
            },  
            error: function (data, status, e){           //相当于java中catch语句块的用法  
                $("#shop-global-alert").text("上传失败，请检查！").show(500).delay(2000).hide(500);
            }  
        });  
   });
   
   //menu_index 序号更改
   $(".menu_index").blur(function() {
       var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val()),
            menuIndex = $.trim($(this).val()),
            regxNumber = /^\d+$/,
            $this = $(this);

          if (regxNumber.test(menuIndex)) {
              $.post(
                      "/shop/menuIndexUpdate/" + menuId + "/" + menuIndex,
                      function(responce) {
                          if (responce.responce == "FAILED") {
                              $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                          } else {
                              $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                          }
                      },
                      "json"
              )
          } else {
              $("#shop-global-alert").text("您输入的数字格式不正确！").show(500).delay(2000).hide(500);
          }
   });
   
   //打折
   $(".discount-check").change(function(){
      if ($(this).attr("checked") === "checked") {
        var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
        $.post(
          "/shop/setDiscount/",
          {"menuId": menuId},
          function(msg) {
              if (msg.responce === "SUCCESS") {
                   alert(msg.message);
                   //window.location.reload();
              } else {
                  alert(msg.message);
              }
          },
          "json"
        );
           
      } else {
          var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
          $.post(
            "/shop/cancelDiscount/",
            {"menuId": menuId},
            function(msg) {
                if (msg.responce === "SUCCESS") {
                     alert(msg.message);
                     //window.location.reload();
                } else {
                    alert(msg.message);
                }
            },
            "json"
           );
      }
   });
   
   //设置新菜
   $(".new-menu-check").change(function(){
      if ($(this).attr("checked") === "checked") {
        var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
        $.post(
          "/shop/setNewMenu/",
          {"menuId": menuId},
          function(msg) {
              if (msg.responce === "SUCCESS") {
                   alert(msg.message);
              } else {
                  alert(msg.message);
              }
          },
          "json"
        );
           
      } else {
          var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
          $.post(
            "/shop/cancelNewMenu/",
            {"menuId": menuId},
            function(msg) {
                if (msg.responce === "SUCCESS") {
                     alert(msg.message);
                } else {
                    alert(msg.message);
                }
            },
            "json"
           );
      }

   });
   
   //设置招牌菜
   $(".special-check").change(function(){
      if ($(this).attr("checked") === "checked") {
        var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
        $.post(
          "/shop/setSpecial/",
          {"menuId": menuId},
          function(msg) {
              if (msg.responce === "SUCCESS") {
                   alert(msg.message);
              } else {
                  alert(msg.message);
              }
          },
          "json"
        );
           
      } else {
          var menuId = $.trim($(this).parents(".menu-list-item").find(".menu_id").val());
          $.post(
            "/shop/cancelSpecial/",
            {"menuId": menuId},
            function(msg) {
                if (msg.responce === "SUCCESS") {
                     alert(msg.message);
                } else {
                    alert(msg.message);
                }
            },
            "json"
           );
      }

   });
   
   //menu修改按钮绑定事件
   $(".update-menu-info").bind("click", showUpdateWrap);
   
   //显示修改面板
   function showUpdateWrap() {
       var $this = $(this),
           $parent = $this.parents(".menu-list-item"),
           menuName = $parent.find(".menu-name").text(),
           menuPrice = $parent.find(".menu-price").text(),
           menuId = $parent.find(".menu_id").val(),
           menuImage = $parent.find(".menu-image-wrap img").attr("src");
       $("#update-menu-name").val(menuName);
       $("#update-menu-price").val(menuPrice);
       $("#update-image-preview").attr("src", menuImage);
       $("#update-menu-id").val(menuId);
       $this.bind("click", cancelUpdate);
       $("#update-menu-image").bind("change", updateMenuImage);
       $("#menu-update-confirm").bind("click", menuUpdateSubmit);
       $(".menu-info-update-wrap").insertAfter($parent).show(400);
   }
   
   //取消修改
   function cancelUpdate(){
        $(".menu-info-update-wrap").hide(600).find("input").val("");
        $(this).unbind("click").bind("click", showUpdateWrap);
         $("#update-menu-image").unbind("change");
         $("#menu-update-confirm").unbind("click");
   }
   
   //图片更新上传
   function updateMenuImage() {
       $.ajaxFileUpload({  
            url:'/shop/menuImageUpdate/',            //需要链接到服务器地址  
            secureuri:false,  
            fileElementId:'update-menu-image',                        //文件选择框的id属性  
            dataType: 'json',                                     //服务器返回的格式，可以是json  
            success: function (data, status){             //相当于java中try语句块的用法      
                if (data.responce == "SUCCESS") {
                     $("#update-image-preview").attr("src", data.message).show();
                     $("#shop-global-alert").text("图片上传成功！").show(500).delay(2000).hide(500);
                } else {
                    $("#shop-global-alert").text(data.message).show(500).delay(2000).hide(500);
                }
               
            },  
            error: function (data, status, e){           //相当于java中catch语句块的用法  
                $("#shop-global-alert").text("上传失败，请检查！").show(500).delay(2000).hide(500);
            }  
        });  
   }
   
   //提交菜单
   function menuUpdateSubmit() {
       var menuName = $("#update-menu-name").val(),
           menuPrice = $("#update-menu-price").val(),
           menuId = $("#update-menu-id").val();
      
      if (menuName == "" || menuPrice == "") {
              $("#shop-global-alert").text("输入不能为空，请重新输入！").show(500).delay(5000).hide(500);
          } else {
              $.post(
                "/shop/updateMenu/" + menuId + "/" + menuName + "/" + menuPrice,
                function(responce) {
                      if (responce.responce == "SUCCESS") {
                           $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                           $(".menu-info-update-wrap").hide(600).find("input").val("");
                            $("#update-menu-image").unbind("change");
                            $("#menu-update-confirm").unbind("click");
                      } else {
                          $("#shop-global-alert").text(responce.message).show(500).delay(2000).hide(500);
                      }
                },
                "json"
              )
          }
   }
   
   $(".menu-list-item").mouseenter(function() {
       $(this).find(".update-menu-info").show(100);
   }).mouseleave(function() {
       $(this).find(".update-menu-info").hide(100);
   })
   
   //店铺查询是否有新的订单、通过轮训
   (function(){
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