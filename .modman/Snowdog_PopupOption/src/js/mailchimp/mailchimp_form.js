(function() {
    var fnames = [],
        ftypes = [],
        err_style = '',
        head = document.getElementsByTagName('head')[0],
        style = document.createElement('style'),
        mce_preload_checks = 0;

    fnames[0] = 'EMAIL';
    ftypes[0] = 'email';
    fnames[1] = 'FNAME';
    ftypes[1] = 'text';
    fnames[2] = 'LNAME';
    ftypes[2] = 'text';
    fnames[3] = 'MMERGE3';
    ftypes[3] = 'number';

    try {
        var jqueryLoaded = jQuery;
        jqueryLoaded = true;
    } catch (err) {
        var jqueryLoaded = false;
    }

    try {
        err_style = mc_custom_error_style;
    } catch (e) {
        err_style = '#mc_embed_signup input.mce_inline_error{border-color:#6B0505;} #mc_embed_signup div.mce_inline_error{margin: 0 0 1em 0; padding: 5px 10px; background-color:#6B0505; font-weight: bold; z-index: 1; color:#fff;}';
    }

    style.type = 'text/css';
    if (style.styleSheet) {
        style.styleSheet.cssText = err_style;
    } else {
        style.appendChild(document.createTextNode(err_style));
    }
    head.appendChild(style);
    setTimeout(mce_preload_check, 250);

    function mce_preload_check() {
        if (mce_preload_checks > 40) return;
        mce_preload_checks++;
        try {
            jqueryLoaded = jQuery;
        } catch (err) {
            setTimeout(mce_preload_check, 250);
            return;
        }
        
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '/js/mailchimp/jquery.form-n-validate.js';
        head.appendChild(script);

        if (are_cookies_enabled() && !isIE()) {
            mce_init_form();
        }
    }

    function isIE() {
        return ((navigator.appName === 'Microsoft Internet Explorer') || ((navigator.appName === 'Netscape') && (new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})").exec(navigator.userAgent) !== null)));
    }

    function are_cookies_enabled() {
        var cookieEnabled = (navigator.cookieEnabled);

        if (typeof navigator.cookieEnabled == "undefined" && !cookieEnabled) {
            document.cookie = "testcookie";
            cookieEnabled = (document.cookie.indexOf("testcookie") !== -1);
        }
        return (cookieEnabled);
    }


    function mce_init_form() {
        jQuery(document).ready(function() {
            var $mcForm = jQuery('#mc-embedded-subscribe-form');
            var $mcClose = jQuery('#mc_embed_signup .mc_embed_close');
            var $mcCloseHref = jQuery('#mc-popup-wrapper a');
            var dataExpired = jQuery('#mc_embed_signup').attr("data-expired");
            var dataLastRestart = jQuery('#mc_embed_signup').attr("data-last_restart");
            var dataRefreshPage = jQuery('#mc_embed_signup').attr("data-refresh_page");
            var dataLastPage = jQuery('#mc_embed_signup').attr("data-last_page");
            var options = {
                errorClass: 'mce_inline_error', errorElement: 'div', onkeyup: function() {
                }, onfocusout: function() {
                }, onblur: function() {
                }
            };
            var mce_validator = $mcForm.validate(options);
            $mcForm.unbind('submit');//remove the validator so we can get into
            // beforeSubmit on the ajaxform, which then calls the validator
            options = {
                url: $mcForm.attr('action'),
                type: 'GET',
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
                beforeSubmit: function() {
                    jQuery('#mce_tmp_error_msg').remove();
                    jQuery('.datefield', '#mc_embed_signup').each(
                        function() {
                            var fields = [];
                            var i = 0;
                            jQuery(':text', this).each(
                                function() {
                                    fields[i] = this;
                                    i++;
                                });
                            jQuery(':hidden', this).each(
                                function() {
                                    var bday = false;
                                    if (fields.length == 2) {
                                        bday = true;
                                        fields[2] = {'value': 1970};//trick birthdays into having years
                                    }
                                    if (fields[0].value == 'MM' && fields[1].value == 'DD' && (fields[2].value == 'YYYY' || (bday && fields[2].value == 1970) )) {
                                        this.value = '';
                                    } else if (fields[0].value === '' && fields[1].value === '' && (fields[2].value === '' || (bday && fields[2].value == 1970) )) {
                                        this.value = '';
                                    } else {
                                        if (/\[day\]/.test(fields[0].name)) {
                                            this.value = fields[1].value + '/' + fields[0].value + '/' + fields[2].value;
                                        } else {
                                            this.value = fields[0].value + '/' + fields[1].value + '/' + fields[2].value;
                                        }
                                    }
                                });
                        });
                    jQuery('.phonefield-us', '#mc_embed_signup').each(
                        function() {
                            var fields = [];
                            var i = 0;
                            jQuery(':text', this).each(
                                function() {
                                    fields[i] = this;
                                    i++;
                                });
                            jQuery(':hidden', this).each(
                                function() {
                                    if (fields[0].value.length != 3 || fields[1].value.length != 3 || fields[2].value.length != 4) {
                                        this.value = '';
                                    } else {
                                        this.value = 'filled';
                                    }
                                });
                        });
                    return mce_validator.form();
                },
                success: mce_success_cb
            };
            // $mcForm.ajaxForm(options);

            jQuery('#mc_embed_signup').hide();
            var cks = document.cookie.split(';');
            var show = true;
            var refreshPageCnt = 0;
            var refreshCokie = false;
            for (var i = 0; i < cks.length; i++) {
                var parts = cks[i].split('=');
                if (jQuery.trim(parts[0]) === "MCEvilPopupClosed" && parts[1] > dataLastRestart) {
                    show = false;
                }
                if(dataRefreshPage  !== '0') {

                    if (jQuery.trim(parts[0]) === "MCEvilRefreshPage" && parts[1] != dataRefreshPage) {
                        refreshPageCnt = parseInt(parts[1]) + 1;
                        show = false;

                    }
                    if (jQuery.trim(parts[0]) === "MCEvilRefreshPage") {
                        refreshCokie = true;
                    }
                }else{
                    refreshCokie = true;
                }

            }
            if(refreshPageCnt == 0){
                refreshPageCnt++;
            }else if(dataLastPage == false){
                refreshPageCnt = 1;
            }

            var now = new Date();
            document.cookie = 'MCEvilRefreshPage=' + refreshPageCnt + ';expires=' + new Date(now.getTime() + 31536000000) + ';path=/';

            if (show && refreshCokie) {
                $mcClose.show();
                setTimeout(function() {
                    jQuery('#mc_embed_signup').fadeIn();
                }, 2000);

                $mcClose.click(function() {
                    mcEvilPopupClose();
                });
                $mcCloseHref.click(function() {
                    mcEvilPopupClose();
                });
            }

            jQuery(document).keydown(function(e) {
                if (e === null) {
                    keycode = event.keyCode;
                } else {
                    keycode = e.which;
                }
                if (keycode == 27) {
                    mcEvilPopupClose();
                }
            });

            function mcEvilPopupClose() {
                var expires_date;
                jQuery('#mc_embed_signup').hide();
                var now = new Date();
                if (dataExpired) {
                    expires_date = new Date();
                    expires_date.setDate(now.getDate() + parseInt(dataExpired));
                } else {
                    expires_date = new Date(now.getTime() + 31536000000);
                }
                document.cookie = 'MCEvilPopupClosed=' + now.getTime() + ';expires=' + expires_date.toGMTString() + ';path=/';
            }
        });
    }

    function mce_success_cb(resp) {
        var $response = jQuery('#mce-' + resp.result + '-response');
        jQuery('#mce-success-response').hide();
        jQuery('#mce-error-response').hide();
        if (resp.result == "success") {
            $response.show();
            $response.html(resp.msg);
            jQuery('#mc-embedded-subscribe-form').each(function() {
                this.reset();
            });
        } else {
            var index = -1;
            var msg;
            try {
                var parts = resp.msg.split(' - ', 2);
                if (parts[1] === undefined) {
                    msg = resp.msg;
                } else {
                    var i = parseInt(parts[0]);
                    if (i.toString() == parts[0]) {
                        index = parts[0];
                        msg = parts[1];
                    } else {
                        index = -1;
                        msg = resp.msg;
                    }
                }
            } catch (e) {
                index = -1;
                msg = resp.msg;
            }
            try {
                if (index == -1) {
                    $response.show();
                    $response.html(msg);
                } else {
                    var err_id = 'mce_tmp_error_msg';
                    var html = '<div id="' + err_id + '" style="' + err_style + '"> ' + msg + '</div>';

                    var input_id = '#mc_embed_signup';
                    var f = jQuery(input_id);
                    if (ftypes[index] == 'address') {
                        input_id = '#mce-' + fnames[index] + '-addr1';
                        f = jQuery(input_id).parent().parent().get(0);
                    } else if (ftypes[index] == 'date') {
                        input_id = '#mce-' + fnames[index] + '-month';
                        f = jQuery(input_id).parent().parent().get(0);
                    } else {
                        input_id = '#mce-' + fnames[index];
                        f = jQuery().parent(input_id).get(0);
                    }
                    if (f) {
                        jQuery(f).append(html);
                        jQuery(input_id).focus();
                    } else {
                        $response.show();
                        $response.html(msg);
                    }
                }
            } catch (e) {
                $response.show();
                $response.html(msg);
            }
        }
    }
})(jQuery);
