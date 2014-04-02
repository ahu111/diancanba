$(function(){
    $(".grade-list").bind("change", submitGrade);

    function submitGrade() {
        var grade = $(this).val(),
            menuId = $(this).parents(".list-group-item").find(".menu-id").val(),
            remark = $(this).parents(".list-group-item").find(".menu-remark").val(),
            orderId = $(this).parents(".list-group-item").find(".order-id").val();
    
        if (grade === 0) {
            return;
        }

        if (grade > 5) {
            grade = 5;
        } else if (grade < 0) {
            grade = 1;
        } 

        $.post(
            "/order/menuGrade/",
            {"menuId": menuId, "orderId": orderId, "grade": grade, "remark": remark},
            function(responce) {
                if (responce.responce === "SUCCESS") {
                    alert(responce.message);
                    window.location.reload();
                } else {
                    alert(responce.message);
                }
            },
            "json"
        )
    }
})


