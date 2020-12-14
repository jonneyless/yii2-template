/**
 * 基于 JQuery 的计时插件
 *
 * @author Jony
 * @version 2014-03-15 22:08 Jony <jonneyless@gmail.com>
 */
;(function ($) {
    var timerIndex;
    var timerBox = [];
    var timerConfig = [];
    $.fn.extend({
        /**
         * @param 整型 beginTime    起始时间，默认为 0。格式：hhiiss
         * @param 布尔 countDown    倒计时，默认为 false
         * @param 字串 clockClass    指针样式，默认为 clock
         * @param 字串 dotClass        间隔样式，默认为 dot
         * @param 字串 firstClass    前导样式，默认为 first
         * @example $('#timer').jqTimer({begintTime: 123456});
         */
        jqTimer: function (options) {
            var defaults = {
                beginTime: 0, countdown: false, clockClass: 'clock', dotClass: 'dot', firstClass: 'first'
            };
            timerIndex = timerBox.length;
            timerBox[timerIndex] = this;
            timerConfig[timerIndex] = $.extend(defaults, options || {});
            var box = timerBox[timerIndex];
            var config = timerConfig[timerIndex];
            box.addClass('timer');
            box.append($('<div>').addClass(config.clockClass).text(config.beginTime[0]))
            .append($('<div>').addClass(config.clockClass).text(config.beginTime[1]))
            .append($('<div>').addClass(config.dotClass).text(':'))
            .append($('<div>').addClass(config.clockClass).addClass(config.firstClass).text(config.beginTime[2]))
            .append($('<div>').addClass(config.clockClass).text(config.beginTime[3]))
            .append($('<div>').addClass(config.dotClass).text(':'))
            .append($('<div>').addClass(config.clockClass).addClass(config.firstClass).text(config.beginTime[4]))
            .append($('<div>').addClass(config.clockClass).text(config.beginTime[5]));
            box.children('.' + config.clockClass).each(function () {
                var s = parseInt($(this).text());
                s = isNaN(s) ? 0 : s;
                $(this).html('<ul><li>' + s + '</li><li>' + s + '</li></ul>');
            });
            $.extend({
                jqTimerRun: function (boxIndex, i) {
                    var box = timerBox[boxIndex];
                    var config = timerConfig[boxIndex];
                    var i = (!i && i != 0) ? 5 : i;
                    var c = box.children('.' + config.clockClass).eq(i);
                    var p = config.countdown ? (c.hasClass(config.firstClass) ? 5 : 9) : (c.hasClass(config.firstClass) ? 6 : 10);
                    var s = parseInt(c.find('li').eq(0).text(), 10);
                    var h = c.find('li').eq(0).height();
                    var g = false;
                    if (config.countdown) {
                        s--;
                        if (s < 0) {
                            s = p;
                            g = true;
                        }
                        c.children('ul').css({marginTop: '-' + h + 'px'});
                        c.find('li').eq(0).text(s);
                        c.children('ul').animate({marginTop: '0px'}, 'fast', function () {
                            c.find('li').eq(1).text(s);
                        });
                    } else {
                        s++;
                        if (s >= p) {
                            s = 0;
                            g = true;
                        }
                        c.children('ul').css({marginTop: '0px'});
                        c.find('li').eq(1).text(s);
                        c.children('ul').animate({marginTop: '-' + h + 'px'}, 'fast', function () {
                            c.find('li').eq(0).text(s);
                        });
                    }
                    if (g && i > 0) {
                        $.jqTimerRun(boxIndex, (i - 1));
                    }
                }
            });
            setInterval('$.jqTimerRun("' + timerIndex + '")', 1000);
        }
    });
})(jQuery);