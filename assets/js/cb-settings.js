jQuery(document).ready(function($) {

    // Position Preview
    let previewWrap = $('.custom-banner-preview-wrapper');
    let preview = $('.custom-banner-preview-wrapper').find('.custom-banner-preview');

    function positionPreview() {
        let adminBarH = $('#wpadminbar').height();
        let previewW = $('#wpcontent').width() + 'px';
        let previewOffsetTop = previewWrap.offset().top - adminBarH;
        if ($(window).scrollTop() > previewOffsetTop) {
            previewWrap.height(preview.height());
            preview.addClass('fixed');
            preview.css('top', adminBarH);
            preview.css('width', previewW);
        } else {
            preview.removeClass('fixed');
            preview.css('top', 'unset');
            preview.css('width', '100%');
        }
    }
    positionPreview();

    $(window).scroll(function() {
        positionPreview();
    });

    $(window).resize(function() {
        positionPreview();
    });
    
    // Update Preview
    let swiperObject = null;
    let ajaxCallId = 0;
    function UpdatePreview() {
        $('.custom-banner-preview').addClass('loading');
        let currentCallId = ++ajaxCallId;

        setTimeout(function() {
            let data = {
                'action': 'custom_banner_update_preview',
                'nonce': customBannerAjax.nonce,
                'custom_banner_enable': $('input[name="custom_banner_enable"]').is(':checked') ? 1 : 0,
                'custom_banner_text_color': $('input[name="custom_banner_text_color"]').val(),
                'custom_banner_background_color': $('input[name="custom_banner_background_color"]').val(),
                'custom_banner_width': $('input[name="custom_banner_width"]:checked').val(),
                'custom_banner_arrows': $('input[name="custom_banner_arrows"]:checked').val(),
                'custom_banner_content' : GetPreviewContent(),
                'custom_banner_css_class' : $('input[name="custom_banner_css_class"]').val(),
                'custom_banner_autoplay' : $('input[name="custom_banner_autoplay"]').is(':checked') ? 'on' : '',
                'custom_banner_delay' : $('input[name="custom_banner_delay"]').val(),
            };

            $.post(customBannerAjax.ajaxUrl, data, function(response) {
                if (currentCallId === ajaxCallId) {
                    $('.custom-banner-preview').removeClass('loading');
                    $('.custom-banner-preview').html(response);
                    positionPreview();
                    ReloadSwiper(swiperObject);
                }
            });
        }, 100);
    }

    UpdatePreview();
    $(document).on('change', '.custom-banner-settings-wrap input:not(.wp-picker-default):not([name=custom_banner_enable]), .custom-banner-settings-wrap textarea', function() {
        UpdatePreview();
    });

    function GetPreviewContent() {
        var bannerTextArray = [];
        $('#custom-banner-content-table tr').each(function() {

            var row = $(this);
            var bannerText = {
                type: row.find('select[name^="custom_banner_banner_text"][name$="[type]"]').val(),
                text: row.find('input[name^="custom_banner_banner_text"][name$="[text]"]').val(),
                link_text: row.find('input[name^="custom_banner_banner_text"][name$="[link_text]"]').val(),
                url: row.find('input[name^="custom_banner_banner_text"][name$="[url]"]').val(),
                show_link: row.find('input[name^="custom_banner_banner_text"][name$="[show_link]"]').is(':checked') ? 'on' : 'off'
            };

            if (bannerText.type === 'html') {
                bannerText.text = row.find('textarea[name^="custom_banner_banner_text"][name$="[text]"]').val();
                bannerText.text = unescape(bannerText.text);
                delete bannerText.link_text;
                delete bannerText.url;
                delete bannerText.show_link;
            }

            bannerTextArray.push(bannerText);
        });

        return bannerTextArray;
    }

    // Initiates a Swiper object
    function ReloadSwiper(swiper) {
        if (swiper != null) {
            swiper.destroy(true, true);
        }
        
        // Get current settings
        let autoplay = $('input[name="custom_banner_autoplay"]').is(':checked') ? 'on' : '';
        let delay = $('input[name="custom_banner_delay"]').val();

        // Create new Swiper
        let swiperArgs = {
            direction: "horizontal",
            speed: 400,
            loop: true,
            navigation: {
                nextEl: ".banner-next",
                prevEl: ".banner-prev",
            },
        }
        if (autoplay === "on" && !isNaN(delay)) {
            swiperArgs.autoplay = {
                delay: delay * 1000,
                disableOnInteraction: false,
            }
        }
        swiper = new Swiper(".bannerSwiper", swiperArgs);
    }

    // Color Picker
    $('.custom-banner-color-field').wpColorPicker({
        change: function() {
            UpdatePreview();
        },
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.wp-picker-container').length && !$(e.target).is('.wp-color-picker, .wp-color-result')) {
            $('.custom-banner-color-field').iris('hide');
        } 
    });

    $('.wp-color-picker').on('click', function(e) {
        $('.custom-banner-color-field').iris('hide');
        $(e.target).closest('.custom-banner-color-field').iris('show');
    });

    $('.wp-color-result').on('click', function(e) {
        $('.custom-banner-color-field').iris('hide');
    });

    // Add a message
    $('#cbAddMore').click(function() {
        var index = $('#custom-banner-content-table tr').length;
        var innerHTML = '<tr><td><label>Type</label><select name="custom_banner_banner_text[' + index + '][type]" class="Type"><option value="simple">Simple</option><option value="html">HTML</option></select></td>';
        innerHTML += '<td class="message"><label>Text</label><input type="text" name="custom_banner_banner_text[' + index + '][text]"></td>';
        innerHTML += '<td><label>Link Text</label><input type="text" name="custom_banner_banner_text[' + index + '][link_text]"></td>';
        innerHTML += '<td><label>URL</label><input type="text" name="custom_banner_banner_text[' + index + '][url]"></td>';
        innerHTML += '<td class="switch"><label>Show Link</label><input type="checkbox" name="custom_banner_banner_text[' + index + '][show_link]" checked="checked"></td>';
        innerHTML += '<td><button type="button" class="remove-row"><span class="minus"></span></button></td></tr>';
        $('#custom-banner-content-table').append(innerHTML);
        UpdatePreview();
    });

    // Remove a message
    $(document).on('click', '#custom-banner-content-table .remove-row', function() {
        $(this).closest('tr').remove();
        updateIndices();
        UpdatePreview();
    });

    // Update message table indices
    function updateIndices() {
        $('#custom-banner-content-table tr').each(function(index) {
            $(this).find('select, textarea, input').each(function() {
                var name = $(this).attr('name').match(/(\w+)\[(\d+)\](\[\w+\])/);
                if (name) {
                    $(this).attr('name', name[1] + '[' + index + ']' + name[3]);
                }
            });
        });
    }

    // Change message types
    $('#custom-banner-content-table').on('change', '.type', function() {
        var row = $(this).closest('tr');
        var index = row.index();
        var type = $(this).val();
        var innerHTML = '<td><label>Type</label><select name="custom_banner_banner_text[' + index + '][type]" class="type">';
        if (type === 'html') {
            innerHTML += '<option value="simple">Simple</option><option value="html" selected>HTML</option></select></td>';
            innerHTML += '<td class="html"><label>HTML</label><textarea onInput="textAreaAdjust(this)" name="custom_banner_banner_text[' + index + '][text]"></textarea></td>';
        } else {
            innerHTML += '<option value="simple" selected>Simple</option><option value="html">HTML</option></select></td>';
            innerHTML += '<td class="message"><label>Text</label><input type="text" name="custom_banner_banner_text[' + index + '][text]"></td>';
            innerHTML += '<td><label>Link Text</label><input type="text" name="custom_banner_banner_text[' + index + '][link_text]"></td>';
            innerHTML += '<td><label>URL</label><input type="text" name="custom_banner_banner_text[' + index + '][url]"></td>';
            innerHTML += '<td class="switch"><label>Show Link</label><input type="checkbox" name="custom_banner_banner_text[' + index + '][show_link]"></td>';
        }
        innerHTML += '<td><button type="button" class="remove-row"><span class="minus"></span></button></td>';
        row.html(innerHTML);
        UpdatePreview();
    });

    // Number input
    $('.custom-banner-number-field').each(function() {

        var minus = $(this).find('button.minus-btn');
        var plus = $(this).find('button.plus-btn');
        var input = $(this).find('input[type="number"]');

        // Decrement value
        minus.click(function() {
            var value = parseInt( input.val(), 10) || 0;
            if (value > parseInt( input.attr('min'), 10)) {
                input.val(value - 1);
                input.trigger('change');
            }
        });

        // Increment value
        plus.click(function() {
            var value = parseInt( input.val(), 10) || 0;
            if (value < parseInt( input.attr('max'), 10)) {
                input.val(value + 1);
                input.trigger('change');
            }
        });
    });

    // Advanced settings dropdown
    $('.custom-banner-adv-settings .form-table').css('display', 'none');
    $('.custom-banner-adv-settings h2').on('click', function() {
        var parent = $(this).parent('.custom-banner-adv-settings')
        parent.toggleClass('open');
        if (parent.hasClass('open')) {
            $(this).next('.form-table').slideDown();
        } else {
            $(this).next('.form-table').slideUp();
        }
    });
});

