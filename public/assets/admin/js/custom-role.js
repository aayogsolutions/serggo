$(document).ready(function() {
    $(".pos_management").on('change', function (){
        if ($(".pos_management:checked").length == $(".pos_management").length) {
            $("#pos_management").prop("checked", true);
        } else {
            $("#pos_management").prop("checked", false);
        }
    });

    $("#pos_management").change(function (){
        if ($("#pos_management").is(":checked")) {
            $(".pos_management").prop("checked", true);
        } else {
            $(".pos_management").prop("checked", false);
        }
    });

    $(".order_management").on('change', function (){
        if ($(".order_management:checked").length == $(".order_management").length) {
            $("#order_management").prop("checked", true);
        } else {
            $("#order_management").prop("checked", false);
        }
    });

    $("#order_management").change(function (){
        if ($("#order_management").is(":checked")) {
            $(".order_management").prop("checked", true);
        } else {
            $(".order_management").prop("checked", false);
        }
    });

    $(".product_management").on('change', function (){
        if ($(".product_management:checked").length == $(".product_management").length) {
            $("#product_management").prop("checked", true);
        } else {
            $("#product_management").prop("checked", false);
        }
    });

    $("#product_management").change(function (){
        if ($("#product_management").is(":checked")) {
            $(".product_management").prop("checked", true);
        } else {
            $(".product_management").prop("checked", false);
        }
    });

    $(".promotion_management").on('change', function (){
        if ($(".promotion_management:checked").length == $(".promotion_management").length) {
            $("#promotion_management").prop("checked", true);
        } else {
            $("#promotion_management").prop("checked", false);
        }
    });

    $("#promotion_management").change(function (){
        if ($("#promotion_management").is(":checked")) {
            $(".promotion_management").prop("checked", true);
        } else {
            $(".promotion_management").prop("checked", false);
        }
    });

    $(".support_management").on('change', function (){
        if ($(".support_management:checked").length == $(".support_management").length) {
            $("#support_management").prop("checked", true);
        } else {
            $("#support_management").prop("checked", false);
        }
    });

    $("#support_management").change(function (){
        if ($("#support_management").is(":checked")) {
            $(".support_management").prop("checked", true);
        } else {
            $(".support_management").prop("checked", false);
        }
    });

    $(".report_management").on('change', function (){
        if ($(".report_management:checked").length == $(".report_management").length) {
            $("#report_management").prop("checked", true);
        } else {
            $("#report_management").prop("checked", false);
        }
    });

    $("#report_management").change(function (){
        if ($("#report_management").is(":checked")) {
            $(".report_management").prop("checked", true);
        } else {
            $(".report_management").prop("checked", false);
        }
    });

    $(".user_management").on('change', function (){
        if ($(".user_management:checked").length == $(".user_management").length) {
            $("#user_management").prop("checked", true);
        } else {
            $("#user_management").prop("checked", false);
        }
    });

    $("#user_management").change(function (){
        if ($("#user_management").is(":checked")) {
            $(".user_management").prop("checked", true);
        } else {
            $(".user_management").prop("checked", false);
        }
    });

    $(".system_management").on('change', function (){
        if ($(".system_management:checked").length == $(".system_management").length) {
            $("#system_management").prop("checked", true);
        } else {
            $("#system_management").prop("checked", false);
        }
    });

    $("#system_management").change(function (){
        if ($("#system_management").is(":checked")) {
            $(".system_management").prop("checked", true);
        } else {
            $(".system_management").prop("checked", false);
        }
    });

    $(".module-permission").on('change', function (){
        if ($(".module-permission:checked").length == $(".module-permission").length) {
            $("#select_all").prop("checked", true);
        } else {
            $("#select_all").prop("checked", false);
        }
    });

    $(".addon_management").on('change', function (){
        if ($(".addon_management:checked").length == $(".addon_management").length) {
            $("#addon_management").prop("checked", true);
        } else {
            $("#addon_management").prop("checked", false);
        }
    });

    $("#addon_management").change(function (){
        if ($("#addon_management").is(":checked")) {
            $(".addon_management").prop("checked", true);
        } else {
            $(".addon_management").prop("checked", false);
        }
    });

    $("#select_all").on('change', function (){
        if ($("#select_all").is(":checked")) {
            $(".module-permission").prop("checked", true);
        } else {
            $(".module-permission").prop("checked", false);
        }
    });

    if ($(".module-permission:checked").length == $(".module-permission").length) {
        $("#select_all").prop("checked", true);
    }
});
