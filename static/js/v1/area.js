$(function() {
    
    //改变select框内容  
    function selectChange($select, data) {
        $select.html("").append($("<option value='0'>请选择</option>"));      //清空原有的
        $.each(data, function(i, e){
           $select.append($("<option value='" + e.area_id + "'>" + e.area_name + "</option>"));
        });
    }

    //点击选择地区
    $("#dist-select").change(function() {
        if ($(this).val() !== 0) {
            var $this = $(this);
            $.post(
                "/area/getAreaList",
                {"flag": "dist", "id": $this.val()},
                function(data) {
                    selectChange($("#area-select"), data.areaList);
                },
                "json"
            );
        }
    });
    
    //点击选择城市
    $("#city-select").change(function() {
        if ($(this).val() !== 0) {
            var $this = $(this);
            $.post(
                "/area/getAreaList",
                {"flag": "city", "id": $this.val()},
                function(data) {
                    if (data.responce === "SUCCESS") {
                        selectChange($("#dist-select"), data.areaList);
                        $("#area-select").html("");
                    } else {
                        alert(data.message);
                    }
                    
                },
                "json"
            );
        }
    });
    
     //点击选择附近的地点
    $("#area-select").change(function() {
        if ($(this).val() !== 0) {
            var $this = $(this);
            $.post(
                "/area/getAreaList",
                {"flag": "area", "id": $this.val()},
                function(data) {
                    if (data.responce !== "SUCCESS") {
                        alert(data.message);
                    }
                },
                "json"
            );
        }
    });
    
    //点击选择省份
    $("#province-select").change(function() {
        if ($(this).val() !== 0) {
            var $this = $(this);
            $.post(
                "/area/getAreaList",
                {"flag": "province", "id": $this.val()},
                function(data) {
                    selectChange($("#city-select"), data.areaList);
                    $("#dist-select").html("");
                    $("#area-select").html("");
                },
                "json"
            );
        }
    });
    
    $("#submit-area").click(function(){
       $.post(
        "/area/checkAreaId",
        function(data) {
            if (data.responce === "SUCCESS") {
                localStorage.setItem("areaId",data.areaId);
                localStorage.setItem("sessionId", data.sessionId);
                window.location.href = "/";
            } else {
                alert(data.message);
            }
        },
        "json"
       );
    });
    
    
    
});

