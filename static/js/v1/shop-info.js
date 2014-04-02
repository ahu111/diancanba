$(function(){
   //修改店铺名字
   $(".shop-name-wrap").bind("click", shopNameWrap);
   
   function shopNameWrap() {
        $(".shop-name-text").hide();
        $(".shop-name-input-wrap").show();
        $(".shop-name-submit").bind("click", shopNameSubmit);
        $(".shop-name-wrap").unbind("click");
   }
   
   //店铺名字修改提交
   function shopNameSubmit(event) {
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopName = $.trim($(".shop-name-input").val()),
           regxShopName = /^.{1,42}$/i;
           
       if (regxShopName.test(shopName)) {
           
           if (shopName === $.trim($(".shop-name-text").text())) {
                $(".shop-name-wrap").bind("click", shopNameWrap);       //专门为了你调了一个冒泡
                $(".shop-name-input-wrap").hide();
                $(".shop-name-submit").unbind("click", shopNameSubmit);
                $(".shop-name-text").show();
               
           } else {
                $.post(
                    "/shop/changeShopName/",
                    {"shopName": shopName},
                    function(msg){
                        if (msg.responce === "SUCCESS") {
                            $(".shop-name-text").val(shopName);
                            $(".shop-name-input-wrap").hide();
                            $(".shop-name-submit").unbind("click", shopNameSubmit);
                            $(".shop-name-text").text(shopName).show();
                            $(".shop-name-wrap").bind("click", shopNameWrap);
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        } else {
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        }
                    },
                    "json"
                )
           }
           
       } else {
           $("#shop-global-alert").text("您的餐厅名字超出规定长度，请检查您的输入是否有误，有任何疑问请联系客服！").show(500).delay(2000).hide(500);
       }
   }
   
   //图片上传绑定
   $(".shop-image").bind("click", shopImageWrap);
   
   //图片上传显示函数
   function shopImageWrap () {
       if ($(".shop-image-upload-wrap").css("display") == "inline") {
           //隐藏
           $(".shop-image-upload-wrap").hide();
           $('.shop-image-submit').unbind("click", shopImageSubmit);
           $(".shop-image-input").unbind("change", uploadShopImage);
       } else {
             $(".shop-image-upload-wrap").css("display", "inline");
             $(".shop-image-input").bind("change", uploadShopImage);
             $('.shop-image-submit').bind("click", shopImageSubmit);
       }
   }
   
   //input有输入就开始上传
   function uploadShopImage() {
       $.ajaxFileUpload({  
            url:'/shop/shopImageUpload/',            //需要链接到服务器地址  
            secureuri:false,  
            fileElementId:'shop-image-input',                        //文件选择框的id属性  
            dataType: 'json',                                     //服务器返回的格式，可以是json  
            success: function (data, status){             //相当于java中try语句块的用法      
                if (data.responce === "SUCCESS") {
                     $(".shop-image").attr("src", data.message);
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
   
   function shopImageSubmit() {
       $.post(
        "/shop/shopImageConfirm/",
        function(msg) {
            if (msg.responce === "SUCCESS") {
                $(".shop-image-upload-wrap").hide();
                $('.shop-image-submit').unbind("click", shopImageSubmit);
                $(".shop-image-input").unbind("change", uploadShopImage);
                $(".shop-image").bind("click", shopImageWrap);
                $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
            } else {
                $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
            }
        },
        "json"
       );
   }
   
   
   //修改店铺起送价
   $(".shop-start-price-wrap").bind("click", shopStartPriceWrap);
   
   function shopStartPriceWrap() {
        $(".shop-start-price-text").hide();
        $(".shop-start-price-input-wrap").show();
        $(".shop-start-price-submit").bind("click", shopStartPriceSubmit);
        $(".shop-start-price-wrap").unbind("click");
   }
   
   //店铺名字修改提交
   function shopStartPriceSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopStartPrice = $.trim($(".shop-start-price-input").val()),
           regxShopStartPrice = /^\d{1,3}(\.)?\d{0,2}$/i;
           
       if (regxShopStartPrice.test(shopStartPrice)) {
           
           if (shopStartPrice === $.trim($(".shop-start-price-text").text())) {
                $(".shop-start-price-wrap").bind("click", shopStartPriceWrap);       //专门为了你调了一个冒泡
                $(".shop-start-price-input-wrap").hide();
                $(".shop-start-price-submit").unbind("click", shopStartPriceSubmit);
                $(".shop-start-price-text").show();
               
           } else {
              
                $.post(
                    "/shop/changeShopStartPrice/",
                    {"shopStartPrice": shopStartPrice},
                    function(msg){
                        if (msg.responce === "SUCCESS") {
                            $(".shop-start-price-text").val(shopStartPrice);
                            $(".shop-start-price-input-wrap").hide();
                            $(".shop-start-price-submit").unbind("click", shopStartPriceSubmit);
                            $(".shop-start-price-text").text(shopStartPrice).show();
                            $(".shop-start-price-wrap").bind("click", shopStartPriceWrap);
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        } else {
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        }
                    },
                    "json"
                )
           }
           
       } else {
           $("#shop-global-alert").text("起送价格式不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   
   //修改店铺订餐电话
   $(".shop-phone-wrap").bind("click", shopPhoneWrap);
   
   function shopPhoneWrap() {
        $(".shop-phone-text").hide();
        $(".shop-phone-input-wrap").show();
        $(".shop-phone-submit").bind("click", shopPhoneSubmit);
        $(".shop-phone-wrap").unbind("click");
   }
   
   //店铺订餐电话修改提交
   function shopPhoneSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopPhone = $.trim($(".shop-phone-input").val()),
           regxShopPhone = /.{0,20}/;
           
       if (regxShopPhone.test(shopPhone)) {
           
           if (shopPhone === $.trim($(".shop-phone-text").text())) {
                $(".shop-phone-wrap").bind("click", shopPhoneWrap);       //专门为了你调了一个冒泡
                $(".shop-phone-input-wrap").hide();
                $(".shop-phone-submit").unbind("click", shopPhoneSubmit);
                $(".shop-phone-text").show();
               
           } else {
              
                $.post(
                    "/shop/changeShopPhone/",
                    {"shopPhone": shopPhone},
                    function(msg){
                        if (msg.responce === "SUCCESS") {
                            $(".shop-phone-text").val(shopPhone);
                            $(".shop-phone-input-wrap").hide();
                            $(".shop-phone-submit").unbind("click", shopPhoneSubmit);
                            $(".shop-phone-text").text(shopPhone).show();
                            $(".shop-phone-wrap").bind("click", shopPhoneWrap);
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        } else {
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        }
                    },
                    "json"
                )
           }
           
       } else {
           $("#shop-global-alert").text("订餐电话不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   //修改送餐速度
   $(".shop-speed-wrap").bind("click", shopSpeedWrap);
   
   function shopSpeedWrap() {
        $(".shop-speed-text").hide();
        $(".shop-speed-input-wrap").show();
        $(".shop-speed-submit").bind("click", shopSpeedSubmit);
        $(".shop-speed-wrap").unbind("click");
   }
   
   //送餐速度修改提交
   function shopSpeedSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopSpeed = $.trim($(".shop-speed-input").val()),
           regxShopSpeed = /^.{0,200}$/;
           
       if (regxShopSpeed.test(shopSpeed)) {
           
           if (shopSpeed === $.trim($(".shop-speed-text").text())) {
                $(".shop-speed-wrap").bind("click", shopSpeedWrap);       //专门为了你调了一个冒泡
                $(".shop-speed-input-wrap").hide();
                $(".shop-speed-submit").unbind("click", shopSpeedSubmit);
                $(".shop-speed-text").show();
               
           } else {
              
                $.post(
                    "/shop/changeShopSpeed/",
                    {"shopSpeed": shopSpeed},
                    function(msg){
                        if (msg.responce === "SUCCESS") {
                            $(".shop-speed-text").val(shopSpeed);
                            $(".shop-speed-input-wrap").hide();
                            $(".shop-speed-submit").unbind("click", shopSpeedSubmit);
                            $(".shop-speed-text").text(shopSpeed).show();
                            $(".shop-speed-wrap").bind("click", shopSpeedWrap);
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        } else {
                            $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                        }
                    },
                    "json"
                )
           }
           
       } else {
           $("#shop-global-alert").text("送餐速度长度不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   //修改店铺简介
   $(".shop-introduce-wrap").bind("click", shopIntroduceWrap);
   
   function shopIntroduceWrap() {
        $(".shop-introduce-text").hide();
        $(".shop-introduce-input-wrap").show();
        $(".shop-introduce-submit").bind("click", shopIntroduceSubmit);
        $(".shop-introduce-wrap").unbind("click");
   }
   
   //店铺简介提交修改
   function shopIntroduceSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopIntroduce = $.trim($(".shop-introduce-input").val()),
           regxShopIntroduce = /^.{0,2000}$/;
           
       if (regxShopIntroduce.test(shopIntroduce)) {
            $.post(
                "/shop/changeShopIntroduce/",
                {"shopIntroduce": shopIntroduce},
                function(msg){
                    if (msg.responce === "SUCCESS") {
                        $(".shop-introduce-text").val(shopIntroduce);
                        $(".shop-introduce-input-wrap").hide();
                        $(".shop-introduce-submit").unbind("click", shopIntroduceSubmit);
                        $(".shop-introduce-text").text(shopIntroduce).show();
                        $(".shop-introduce-wrap").bind("click", shopIntroduceWrap);
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    } else {
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    }
                },
                "json"
            );
       } else {
           $("#shop-global-alert").text("店铺简介长度不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   
   //修改店铺通告
   $(".shop-notify-wrap").bind("click", shopNotifyWrap);
   
   function shopNotifyWrap() {
        $(".shop-notify-text").hide();
        $(".shop-notify-input-wrap").show();
        $(".shop-notify-submit").bind("click", shopNotifySubmit);
        $(".shop-notify-wrap").unbind("click");

   }
   
   //店铺简介提交通告
   function shopNotifySubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopNotify = $.trim($(".shop-notify-input").val()),
           regxShopNotify = /^.{0,2000}$/;
           
       if (regxShopNotify.test(shopNotify)) {
        
            $.post(
                "/shop/changeShopNotify/",
                {"shopNotify": shopNotify},
                function(msg){
                    if (msg.responce === "SUCCESS") {
                        $(".shop-notify-text").val(shopNotify);
                        $(".shop-notify-input-wrap").hide();
                        $(".shop-notify-submit").unbind("click", shopNotifySubmit);
                        $(".shop-notify-text").text(shopNotify).show();
                        $(".shop-notify-wrap").bind("click", shopNotifyWrap);
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    } else {
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    }
                },
                "json"
            );
       } else {
           $("#shop-global-alert").text("店铺简介长度不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   //修改店铺地址
   $(".shop-address-wrap").bind("click", shopAddressWrap);
   
   function shopAddressWrap() {
        $(".shop-address-text").hide();
        $(".shop-address-input-wrap").show();
        $(".shop-address-submit").bind("click", shopAddressSubmit);
        $(".shop-address-wrap").unbind("click");
   }
   
   //店铺地址修改
   function shopAddressSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopAddress = $.trim($(".shop-address-input").val()),
           regxShopAddress = /^.{0,500}$/;
           
       if (regxShopAddress.test(shopAddress)) {
            $.post(
                "/shop/changeShopAddress/",
                {"shopAddress": shopAddress},
                function(msg){
                    if (msg.responce === "SUCCESS") {
                        $(".shop-address-text").val(shopAddress);
                        $(".shop-address-input-wrap").hide();
                        $(".shop-address-submit").unbind("click", shopAddressSubmit);
                        $(".shop-address-text").text(shopAddress).show();
                        $(".shop-address-wrap").bind("click", shopAddressWrap);
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    } else {
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    }
                },
                "json"
            );
       } else {
           $("#shop-global-alert").text("地址格式不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   
   //修改营业时间
   $(".shop-time-wrap").bind("click", shopTimeWrap);
   
   function shopTimeWrap() {
        $(".shop-time-text").hide();
        $(".shop-time-input-wrap").show();
        $(".shop-time-submit").bind("click", shopTimeSubmit);
        $(".shop-time-wrap").unbind("click");
   }
   
   //店铺营业时间修改
   function shopTimeSubmit(event) {
       
       event.stopPropagation();         //阻止事件冒泡   
       
       var shopTime = $.trim($(".shop-time-input").val()),
           regxShopTime = /^.{0,200}$/;
           
       if (regxShopTime.test(shopTime)) {
            $.post(
                "/shop/changeShopTime/",
                {"shopTime": shopTime},
                function(msg){
                    if (msg.responce === "SUCCESS") {
                        $(".shop-time-text").val(shopTime);
                        $(".shop-time-input-wrap").hide();
                        $(".shop-time-submit").unbind("click", shopTimeSubmit);
                        $(".shop-time-text").text(shopTime).show();
                        $(".shop-time-wrap").bind("click", shopTimeWrap);
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    } else {
                        $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                    }
                },
                "json"
            );
       } else {
           $("#shop-global-alert").text("地址格式不符合规范，请修改后重新输入！").show(500).delay(2000).hide(500);
       }
   }
   
   
    //商铺状态改变
    $(".shop-status").click(function() {
       var flag = $(this).val();
       $.post(
            "/shop/shopStatus/",
            {"status": flag},
             function(msg) {
                 if (msg.responce === "SUCCESS") {
                    $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                } else {
                    $("#shop-global-alert").text(msg.message).show(500).delay(2000).hide(500);
                }
             },
             "json"
        )
    });
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

