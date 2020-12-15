$(document).ready(function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small');
    } else {
        $('body').removeClass('body-small');
    }
    $('#side-menu').metisMenu();
    // Collapse ibox function
    $(document).on('click', '.collapse-link', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.children('.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });
    // Close ibox function
    $(document).on('click', '.close-link', function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });
    // Fullscreen ibox function
    $(document).on('click', '.fullscreen-link', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });
    $('.sidebar-container').slimScroll({
        height: '100%', railOpacity: 0.4, wheelStep: 10
    });
    $('.navbar-minimalize').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    function fix_height() {
        var navbarheight = $('nav.navbar-default').height();
        var wrapperHeight = $('#page-wrapper').height();
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebar-panel").css("min-height", heightWithoutNavbar + "px");
        if (navbarheight > wrapperHeight) {
            $('#page-wrapper').css("min-height", navbarheight + "px");
        }
        if (navbarheight < wrapperHeight) {
            $('#page-wrapper').css("min-height", $(window).height() + "px");
        }
        if ($('body').hasClass('fixed-nav')) {
            if (navbarheight > wrapperHeight) {
                $('#page-wrapper').css("min-height", navbarheight + "px");
            } else {
                $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
            }
        }
    }

    fix_height();
    $(window).bind("load", function () {
        if ($("body").hasClass('fixed-sidebar')) {
            $('.sidebar-collapse').slimScroll({
                height: '100%', railOpacity: 0.9
            });
        }
    });
    $(window).bind("resize", function () {
        if ($(this).width() < 769) {
            $('body').addClass('body-small');
        } else {
            $('body').removeClass('body-small');
        }
    });
    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });
    $(document).tooltip({
        selector: "[data-toggle=tooltip]", container: "body"
    });
    $("[data-toggle=popover]").popover();
    $(document).on('click', "a.btn-ajax", function () {
        var btn = $(this);
        var url = $(this).attr('href');
        $.get(url, function (data) {
            if (data.error) {
                toastr.error(data.msg);
            } else {
                if (data.msg) {
                    toastr.success(data.msg);
                }
                if (data.btn) {
                    btn.text(data.btn.text);
                    btn.attr('title', data.btn.text);
                    if (data.btn.class) {
                        btn.attr('class', data.btn.class);
                    }
                    if (data.btn.href) {
                        btn.attr('href', data.btn.href);
                    }
                }
            }
        }, 'json');
        return false;
    });
    $('.full-height-scroll').slimscroll({
        height: '100%'
    });
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    $(document).on('click', 'a[rel$=picker]', function () {
        var button = $(this);
        var type = button.attr('rel');
        var input = button.closest('.input-group').children('input');
        if (type == 'datetimepicker') {
            input.datetimepicker('show');
        } else {
            input.datepicker('show');
        }
    });
});

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(function () {
            $('#side-menu').fadeIn(400);
        }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout(function () {
            $('#side-menu').fadeIn(400);
        }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
    }
}