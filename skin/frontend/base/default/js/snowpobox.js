(function ($) {
    $(document).ready(function () {
        $('#po-checkbox').change(function() {
            var url = $('#po-checkbox').attr('data-url');
            var data = null;
            if ($("#po-checkbox").is(':checked')) {
                data = 1;
            } else {
                data = 0
            }
            $.ajax({
                url: url,
                type: "POST",
                data: "poBox="+data,
                success: function (data) {
                }
            });
        });

    });
})
(jQuery);