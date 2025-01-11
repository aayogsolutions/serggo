function readURL(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].type.split("/")[0] == 'video') {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewervideo').attr('src', e.target.result);
            }
            $('#viewervideo').css("display", "block");
            $('#viewer').css("display", "none");

            reader.readAsDataURL(input.files[0]);

            var video = $("#viewervideo");

            // Get the video dimensions once it's loaded
            video.on("loadedmetadata", function () {
                var width = video.get(0).videoWidth;
                var height = video.get(0).videoHeight;

                $("#videoWidth").val(width);
                $("#videoHeight").val(height);
                
                console.log("Video dimensions: " + width + " x " + height);
            });
        } else {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            $('#viewer').css("display", "block");
            $('#viewervideo').css("display", "none");
            reader.readAsDataURL(input.files[0]);
        }
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
});

$('.show-item').on('change', function(){
    let type = $(this).val();
    console.log(type);
    show_item(type);
})

function show_item(type) {
    if (type == 'product') {
        $("#type-product").show();
        $("#type-category").hide();
    } else {
        $("#type-product").hide();
        $("#type-category").show();
    }
}

$(document).ready(function() {
    if($('.show-item').val() == 'product')
    {
        $("#type-product").css("display", 'block');
        $("#type-category").css("display", 'none');
    }else{
        $("#type-product").css("display", 'none');
        $("#type-category").css("display", 'block');
    };
});

$('#ui_type').on('change', function(){
    let type = $(this).val();
    show_section(type);
})

function show_section(type) {
    if (type == 'user_product') {
        $("#user_product").show();
        $("#user_service").hide();
    } else if (type == 'user_service') {
        $("#user_product").hide();
        $("#user_service").show();
    } else {
        $("#user_product").hide();
        $("#user_service").hide();
    }
}