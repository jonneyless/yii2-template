(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    $.extend($.summernote.plugins, {
        'emoji': function (context) {
            var self = this;
            var ui = $.summernote.ui;
            var emojis = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '1f30d', '1f30e', '1f30f', '1f31a', '1f31b', '1f31c', '1f31d', '1f31e', '1f31f', '1f331', '1f332', '1f333', '1f334', '1f335', '1f337', '1f338', '1f339', '1f33a', '1f33b', '1f33c', '1f33d', '1f33e', '1f33f', '1f340', '1f341', '1f342', '1f343', '1f344', '1f345', '1f346', '1f347', '1f348', '1f349', '1f34a', '1f34b', '1f34c', '1f34d', '1f34e', '1f34f', '1f350', '1f351', '1f352', '1f353', '1f354', '1f355', '1f356', '1f357', '1f358', '1f359', '1f35a', '1f35b', '1f35c', '1f35d', '1f35e', '1f35f', '1f360', '1f361', '1f362', '1f363', '1f364', '1f365', '1f366', '1f367', '1f369', '1f36a', '1f36b', '1f36c', '1f36d', '1f36e', '1f36f', '1f370', '1f371', '1f372', '1f373', '1f374', '1f375', '1f376', '1f377', '1f378', '1f379', '1f37a', '1f37b', '1f37c', '1f380', '1f381', '1f382', '1f383', '1f384', '1f385', '1f388', '1f389', '1f38a', '1f38b', '1f38c', '1f38d', '1f38e', '1f38f', '1f390', '1f40a', '1f40b', '1f40c', '1f40d', '1f40e', '1f40f', '1f41a', '1f41b', '1f41c', '1f41d', '1f41e', '1f41f', '1f42a', '1f42b', '1f42c', '1f42d', '1f42e', '1f42f', '1f436', '1f437', '1f438', '1f439', '1f43a', '1f43b', '1f43c', '1f43d', '1f43e', '1f440', '1f442', '1f443', '1f444', '1f446', '1f447', '1f448', '1f449', '1f44a', '1f44b', '1f44c', '1f44d', '1f44e', '1f44f', '1f450', '1f451', '1f45a', '1f45b', '1f45c', '1f45d', '1f45e', '1f45f', '1f466', '1f467', '1f468', '1f469', '1f46a', '1f46b', '1f46c', '1f46d', '1f46e', '1f46f', '1f470', '1f471', '1f472', '1f473', '1f474', '1f475', '1f476', '1f477', '1f478', '1f479', '1f47a', '1f47b', '1f47c', '1f47d', '1f47f', '1f480', '1f481', '1f482', '1f483', '1f484', '1f485', '1f486', '1f487', '1f488', '1f489', '1f48a', '1f48b', '1f48c', '1f48d', '1f48e', '1f48f', '1f490', '1f491', '1f492', '1f493', '1f494', '1f495', '1f496', '1f497', '1f498', '1f499', '1f49a', '1f49b', '1f49c', '1f49d', '1f49e', '1f49f', '1f51d', '1f51e', '1f600', '1f601', '1f602', '1f603', '1f604', '1f605', '1f606', '1f607', '1f608', '1f609', '1f60a', '1f60b', '1f60c', '1f60d', '1f60e', '1f60f', '1f610', '1f611', '1f612', '1f613', '1f614', '1f615', '1f616', '1f617', '1f618', '1f619', '1f61a', '1f61b', '1f61c', '1f61d', '1f61e', '1f61f', '1f620', '1f621', '1f622', '1f623', '1f624', '1f625', '1f626', '1f627', '1f628', '1f629', '1f62a', '1f62b', '1f62c', '1f62d', '1f62e', '1f62f', '1f630', '1f631', '1f632', '1f633', '1f634', '1f635', '1f636', '1f637', '1f638', '1f639', '1f63a', '1f63b', '1f63c', '1f63d', '1f63e', '1f63f', '1f640', '1f645', '1f646', '1f647', '1f648', '1f649', '1f64a', '1f64b', '1f64c', '1f64d', '1f64e', '1f64f', '261d', '263a', '270a', '270b', '270c'];
            var chunk = function (val, chunkSize) {
                var R = [];
                for (var i = 0; i < val.length; i += chunkSize) R.push(val.slice(i, i + chunkSize));
                return R;
            };
            var pageLock;
            /*IE polyfill*/
            if (!Array.prototype.filter) {
                Array.prototype.filter = function (fun /*, thisp*/) {
                    var len = this.length >>> 0;
                    if (typeof fun != "function") {
                        throw new TypeError();
                    }
                    var res = [];
                    var thisp = arguments[1];
                    for (var i = 0; i < len; i++) {
                        if (i in this) {
                            var val = this[i];
                            if (fun.call(thisp, val, i, this)) {
                                res.push(val);
                            }
                        }
                    }
                    return res;
                };
            }
            var addListener = function () {
                $(document).on('click', '.emoji-close', function () {
                    self.$panel.hide();
                    removeListener();
                });
                $(document).on('click', '.selectEmoji', function () {
                    insert('bg-emoji_' + $(this).attr('data-value'));
                });
                $(document).on('click', '.emoji-button', function (e) {
                    if ($(this).hasClass('active')) {
                        return null;
                    }
                    if (pageLock) {
                        return null;
                    }
                    pageLock = true;
                    var index = $(this).prevAll('.emoji-button').length;
                    to = "to" + index;
                    $('.emoji-button').removeClass('active');
                    $(this).addClass('active');
                    $('.emoji-list').addClass(to);
                    $('.emoji-list').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (e) {
                        var to = e.originalEvent.animationName;
                        var toTranslateX = to + "-translatex";
                        $(this).attr('class', 'emoji-list animated');
                        $(this).addClass(toTranslateX);
                        pageLock = false;
                    });
                });
            };
            var removeListener = function () {
                $(document).off('click', '.emoji-close');
                $(document).off('click', '.selectEmoji');
                $(document).off('click', '.emoji-button');
            };
            var insert = function (emoji_class) {
                var private_emoji_list = {
                    "1": "[不滿]",
                    "2": "[大哭]",
                    "3": "[發火]",
                    "4": "[發怒]",
                    "5": "[怪笑]",
                    "6": "[汗顏]",
                    "7": "[驚訝]",
                    "8": "[開心]",
                    "9": "[害羞]",
                    "10": "[厲害]",
                    "11": "[賣萌]",
                    "12": "[難過]",
                    "13": "[色色]",
                    "14": "[耍帥]",
                    "15": "[睡覺]",
                    "16": "[委屈]",
                    "17": "[為什麼]",
                    "18": "[懵懂]",
                    "19": "[無語]",
                    "20": "[暈]",
                    "21": "[發睏]",
                    "22": "[可憐]",
                    "23": "[哇塞]",
                    "24": "[微笑]",
                    "25": "[no]",
                    "26": "[憋屈]",
                    "27": "[鬼臉]",
                    "28": "[白眼]"
                };
                var emoji_list = [0x1f600, 0x1f601, 0x1f602, 0x1f603, 0x1f604, 0x1f605, 0x1f606, 0x1f607, 0x1f608, 0x1f609, 0x1f610, 0x1f611, 0x1f612, 0x1f613, 0x1f614, 0x1f615, 0x1f616, 0x1f617, 0x1f618, 0x1f619, 0x1f620, 0x1f621, 0x1f622, 0x1f623, 0x1f624, 0x1f625, 0x1f626, 0x1f627, 0x1f628, 0x1f629, 0x1f630, 0x1f631, 0x1f632, 0x1f633, 0x1f634, 0x1f635, 0x1f636, 0x1f637, 0x1f60a, 0x1f60b, 0x1f60c, 0x1f60d, 0x1f60e, 0x1f60f, 0x1f61a, 0x1f61b, 0x1f61c, 0x1f61d, 0x1f61e, 0x1f61f, 0x1f62a, 0x1f62b, 0x1f62c, 0x1f62d, 0x1f62e, 0x1f62f, 0x263a, 0x1f63a, 0x1f63b, 0x1f63c, 0x1f63d, 0x1f63e, 0x1f63f, 0x1f64a, 0x1f638, 0x1f639, 0x1f648, 0x1f649, 0x1f640, 0x1f43b, 0x1f42f, 0x1f43a, 0x1f43c, 0x1f43d, 0x1f43e, 0x1f44a, 0x1f44b, 0x1f44c, 0x1f44d, 0x1f44e, 0x1f44f, 0x1f45a, 0x1f45b, 0x1f45c, 0x1f45d, 0x1f45e, 0x1f45f, 0x1f46a, 0x1f46b, 0x1f46c, 0x1f46d, 0x1f46e, 0x1f46f, 0x1f47a, 0x1f47b, 0x1f47c, 0x1f47d, 0x1f47f, 0x1f48a, 0x1f48b, 0x1f48c, 0x1f48d, 0x1f48e, 0x1f48f, 0x1f49a, 0x1f49b, 0x1f49c, 0x1f49d, 0x1f49e, 0x1f49f, 0x1f51d, 0x1f51e, 0x1f64b, 0x1f64c, 0x1f64d, 0x1f64e, 0x1f64f, 0x1f331, 0x1f332, 0x1f333, 0x1f334, 0x1f335, 0x1f337, 0x1f338, 0x1f339, 0x1f340, 0x1f341, 0x1f342, 0x1f343, 0x1f344, 0x1f345, 0x1f346, 0x1f347, 0x1f348, 0x1f349, 0x1f350, 0x1f351, 0x1f352, 0x1f353, 0x1f354, 0x1f355, 0x1f356, 0x1f37a, 0x1f37b, 0x1f37c, 0x1f38a, 0x1f38b, 0x1f38c, 0x1f38d, 0x1f38e, 0x1f38f, 0x1f40a, 0x1f40b, 0x1f40c, 0x1f40d, 0x1f40e, 0x1f40f, 0x1f41a, 0x1f41b, 0x1f41c, 0x1f41d, 0x1f41e, 0x1f41f, 0x1f42a, 0x1f42b, 0x1f42c, 0x1f42d, 0x1f42e, 0x1f357, 0x1f358, 0x1f359, 0x1f360, 0x1f361, 0x1f362, 0x1f363, 0x1f364, 0x1f365, 0x1f366, 0x1f367, 0x1f368, 0x1f369, 0x1f370, 0x1f371, 0x1f372, 0x1f373, 0x1f374, 0x1f375, 0x1f376, 0x1f377, 0x1f378, 0x1f379, 0x1f380, 0x1f381, 0x1f382, 0x1f383, 0x1f384, 0x1f385, 0x1f388, 0x1f389, 0x1f390, 0x1f436, 0x1f437, 0x1f438, 0x1f439, 0x1f440, 0x1f442, 0x1f443, 0x1f444, 0x1f446, 0x1f447, 0x1f448, 0x1f449, 0x1f450, 0x1f451, 0x1f466, 0x1f467, 0x1f468, 0x1f469, 0x1f470, 0x1f471, 0x1f472, 0x1f473, 0x1f474, 0x1f475, 0x1f476, 0x1f477, 0x1f478, 0x1f479, 0x1f480, 0x1f481, 0x1f482, 0x1f483, 0x1f484, 0x1f485, 0x1f486, 0x1f487, 0x1f488, 0x1f489, 0x1f490, 0x1f491, 0x1f492, 0x1f493, 0x1f494, 0x1f495, 0x1f496, 0x1f497, 0x1f498, 0x1f499, 0x1f34a, 0x1f34b, 0x1f34c, 0x1f34d, 0x1f34e, 0x1f34f, 0x1f35a, 0x1f35b, 0x1f35c, 0x1f35d, 0x1f35e, 0x1f35f, 0x1f36a, 0x1f36b, 0x1f36c, 0x1f36d, 0x1f36e, 0x1f36f, 0x261d, 0x270a, 0x270b, 0x270c, 0x1f645, 0x1f646, 0x1f647, 0x1f30d, 0x1f30e, 0x1f30f, 0x1f31a, 0x1f31b, 0x1f31c, 0x1f31d, 0x1f31e, 0x1f31f, 0x1f33a, 0x1f33b, 0x1f33c, 0x1f33d, 0x1f33e, 0x1f33f];
                var _emoji_list_utf16 = ["￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "☺", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "☝", "✊", "✋", "✌", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿", "￿￿￿￿"];
                var key = emoji_class.substring(emoji_class.indexOf('_') + 1)
                var emojiIndex = function () {
                    var emojiIndex;
                    emoji_list.every(function (emoji, index) {
                        if (emoji.toString(16) == key) {
                            emojiIndex = index;
                            return false;
                        }
                        return true
                    })
                    return emojiIndex;
                }();
                var data = private_emoji_list[key] || _emoji_list_utf16[emojiIndex] || key;
                var img = new Image();
                img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
                img.alt = data;
                img.className = emoji_class;
                context.invoke('editor.insertNode', img);
            }
            var render = function (emojis) {
                var emoList = '<article><ul>';
                /*limit list to 24 images*/
                var emojis = emojis;
                var chunks = chunk(emojis, 84);
                for (var j = 0; j < chunks.length; j++) {
                    emoList += '<li class="emoji-list animated to0-translatex">';
                    for (var i = 0; i < chunks[j].length; i++) {
                        var emo = chunks[j][i];
                        emoList += '<a href="javascript:void(0)" class="selectEmoji emoji-close" data-value="' + emo + '"><i class="bg-emoji_' + emo + '"></i></a>';
                    }
                    emoList += '</li>';
                }
                emoList += '</ul>';
                emoList += '<footer style="transform: translatex(0px);">' + '<var data-index="bg-emoji_icon">';
                for (var j = 0; j < chunks.length; j++) {
                    if (j == 0) {
                        emoList += '<i class="emoji-button active"></i>';
                    } else {
                        emoList += '<i class="emoji-button"></i>';
                    }
                }
                emoList += '</var></footer></article>';
                return emoList;
            };
            var filterEmoji = function (value) {
                var filtered = emojis.filter(function (el) {
                    return el.indexOf(value) > -1;
                });
                return render(filtered);
            };
            // add emoji button
            context.memo('button.emoji', function () {
                // create button
                var button = ui.button({
                    contents: '<i class="fa fa-smile-o"/>', tooltip: 'emoji', click: function () {
                        if (document.emojiSource === undefined) {
                            document.emojiSource = '';
                        }
                        self.$panel.show();
                        $(this).closest('.note-editor').find('.note-editable').trigger('focus');
                        addListener();
                    }
                });
                // create jQuery object from button instance.
                var $emoji = button.render();
                return $emoji;
            });
            // This events will be attached when editor is initialized.
            this.events = {
                // This will be called after modules are initialized.
                'summernote.init': function (we, e) {
                }, // This will be called when user releases a key on editable.
                'summernote.keyup': function (we, e) {
                }
            };
            // This method will be called when editor is initialized by $('..').summernote();
            // You can create elements for plugin
            this.initialize = function () {
                this.$panel = $('<div class="dropdown-menu dropdown-keep-open qoo-emoji-box animated fadeInUp" id="emoji-dropdown">' + '<i class="emoji-close box-angle fa fa-times-circle" aria-hidden="true"></i>' + render(emojis) + '</div>').hide();
                this.$panel.appendTo('body');
            };
            this.destroy = function () {
                this.$panel.remove();
                this.$panel = null;
            };
        }
    });
}));