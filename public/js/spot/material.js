define(function (require) {
    var main = {
        init: function () {
            $("input[name='Material[attribute]']").change(function () {
                var value = $("input[name='Material[attribute]']:checked").val();
                if (value == 1) {
                    $("#warning-container").css("display", "none");
                } else {
                    $("#warning-container").css("display", "block");
                }
            })
        }
    };
    return main;
});
