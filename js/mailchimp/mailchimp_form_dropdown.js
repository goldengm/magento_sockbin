(function() {
    var fnames = [],
        ftypes = [],
        err_style = '',
        head = document.getElementsByTagName('head')[0],
        style = document.createElement('style'),
        mce_preload_checks = 0,
        cks = document.cookie.split(';');

    fnames[0] = 'EMAIL';
    ftypes[0] = 'email';
    fnames[1] = 'FNAME';
    ftypes[1] = 'text';
    fnames[2] = 'LNAME';
    ftypes[2] = 'text';
    fnames[3] = 'MMERGE3';
    ftypes[3] = 'number';

    try {
        err_style = mc_custom_error_style;
    } catch (e) {
        err_style = '#mcm_embed_signup input.mcem_inline_error{border-color:#6B0505;} #mcm_embed_signup' +
            ' div.mcem_inline_error{margin: 0 0 1em 0; padding: 5px 10px; background-color:#6B0505; font-weight: bold; z-index: 1; color:#fff;}';
    }

    style.type = 'text/css';
    if (style.styleSheet) {
        style.styleSheet.cssText = err_style;
    } else {
        style.appendChild(document.createTextNode(err_style));
    }
    head.appendChild(style);

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
            mcem_init_form();
        }
    }

    function mcem_init_form() {
        jQuery(document).ready(function() {
            var $mcForm = jQuery('#mcm-embedded-subscribe-form');
            var $mcClose = jQuery('#mcm_embed_signup .mcm_embed_close');
            var $mcmSignup = jQuery('#mcm_embed_signup');
            var show = true;

            var options = {
                errorClass: 'mcem_inline_error', errorElement: 'div', onkeyup: function() {
                }, onfocusout: function() {
                }, onblur: function() {
                }
            };
            var mcem_validator = $mcForm.validate(options);
            $mcForm.unbind('submit');//remove the validator so we can get into
            // beforeSubmit on the ajaxform, which then calls the validator
            options = {
                url: $mcForm.attr('action'),
                type: 'GET',
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
                beforeSubmit: function() {
                    jQuery('#mcem_tmp_error_msg').remove();
                    jQuery('.datefield', '#mcm_embed_signup').each(
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
                    jQuery('.phonefield-us', '#mcm_embed_signup').each(
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
                    return mcem_validator.form();
                },
                success: mcem_success_cb
            };
            $mcForm.ajaxForm(options);

            jQuery('#mcm_dropdown-header').click(function() {
                $mcmSignup.toggleClass('in');
            });
            $mcClose.click(function() {
                if (!$mcmSignup.hasClass('in')) {
                    $mcmSignup.css('display', 'none');
                    document.cookie = 'MCEvilDropdownClosed=' + true + ';path=/';
                }
            });

            for (var i = 0; i < cks.length; i++) {
                var parts = cks[i].split('=');
                if (parts[0] === " MCEvilDropdownClosed" && parts[1] === 'true') {
                    show = false;
                }
            }
            if (show) {
                $mcmSignup.css('display', 'block');
            }

        });
    }

    function mcem_success_cb(resp) {
        var $response = jQuery('#mcem-' + resp.result + '-response');
        jQuery('#mcem-success-response').hide();
        jQuery('#mcem-error-response').hide();
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
                    var err_id = 'mcem_tmp_error_msg';
                    var html = '<div id="' + err_id + '" style="' + err_style + '"> ' + msg + '</div>';

                    var input_id = '#mcm_embed_signup';
                    var f = jQuery(input_id);
                    if (ftypes[index] == 'address') {
                        input_id = '#mcem-' + fnames[index] + '-addr1';
                        f = jQuery(input_id).parent().parent().get(0);
                    } else if (ftypes[index] == 'date') {
                        input_id = '#mcem-' + fnames[index] + '-month';
                        f = jQuery(input_id).parent().parent().get(0);
                    } else {
                        input_id = '#mcem-' + fnames[index];
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

    mce_preload_check();
})(jQuery);
