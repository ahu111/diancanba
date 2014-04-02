$(function() {
    
    $("#address-modify").bind("click", addressChange);  //修改地址
    $("#phone-modify").bind("click", phoneChange);   //修改电话
    $("#name-modify").bind("click", nameChange);        //修改名字
    $("#order-submit").bind("click", orderSubmit);      //订单提交函数
    
    //地址确认修改
    function addressSubmit() {
        var userAddress = $.trim($("#user-address-input").val()),
            regxAddress = /^.{4,100}$/i;
        if (regxAddress.test(userAddress)) {
            $.post(
                "/user/userAddressChange/",
                {"userAddress": $("#user-address-input").val()},
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        $("#user-address-input").unbind("blur").val(responce.message).hide();
                        $("#user-address-text").text(responce.message).show();
                        $("#address-modify").bind("click", addressChange).show();
                        $(".menu-tab").show();
                    }
                },
                "json"
            );
        } else {
            alert("地址不符合规范，请确认后再提交！");
        }
    }
    
    //电话确认修改
    function phoneSubmit() {
        var userPhone = $.trim($("#user-phone-input").val()),
            regxPhone = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/;
        
        if (regxPhone.test(userPhone)) {
            $.post(
                "/user/userPhoneChange/",
                {"userPhone": $("#user-phone-input").val()},
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        $("#user-phone-input").unbind("blur").val(responce.message).hide();
                        $("#user-phone-text").text(responce.message).show();
                        $("#phone-modify").bind("click", phoneChange).show();
                        $(".menu-tab").show();
                    }
                },
                "json"
            );
        } else {
            alert("电话不符合规范，请确认后再提交！");
        }
    }
    
    //名字确认修改
    function nameSubmit() {
        var userName = $.trim($("#user-name-input").val()),
            regxName = /^.{1,20}$/i;
        
        if (regxName.test(userName)) {
            $.post(
                "/user/userNameChange/",
                {"userName": $("#user-name-input").val()},
                function(responce) {
                    if (responce.responce === "SUCCESS") {
                        $("#user-name-input").unbind("blur").val(responce.message).hide();
                        $("#user-name-text").text(responce.message).show();
                        $("#name-modify").bind("click", nameChange).show();
                        $(".menu-tab").show();
                    }
                },
                "json"
            );
        } else {
            alert("名字不符合规范，请确认后再提交！");
        }
    }
    
    
    
    //地址修改
    function addressChange() {
        $("#user-address-text").hide();
        $("#user-address-input").bind("blur", addressSubmit).show();
        $("#address-modify").unbind("click").hide();
        $(".menu-tab").hide();
    }
    
    //电话修改
    function phoneChange() {
        $("#user-phone-text").hide();
        $("#user-phone-input").bind("blur", phoneSubmit).show();
        $("#phone-modify").unbind("click").hide();
        $(".menu-tab").hide();
    }
    
    //名字修改
    function nameChange() {
        $("#user-name-text").hide();
        $("#user-name-input").bind("blur", nameSubmit).show();
        $("#name-modify").unbind("click").hide();
        $(".menu-tab").hide();
    }
    
    
    //订单提交
    function orderSubmit() {
        var remark = $("#user-remark").val();       //用户留言
        $.post(
            "/rest/orderSubmit/",
            {"userRemark": remark},
            function(responce) {
            
                if (responce.responce === "SUCCESS") {
                    $(".navbar-title").text(responce.message);
                    setTimeout("window.location.href = '/order'", 1000);
                } else if (responce.responce === "FAILED") {
                    alert(responce.message);
                }
            },
            "json"
        );
    }
    
    $(".user-remark").focus(function(){
        $(".menu-tab").hide();
    }).blur(function(){
        $(".menu-tab").show();
    })
})

