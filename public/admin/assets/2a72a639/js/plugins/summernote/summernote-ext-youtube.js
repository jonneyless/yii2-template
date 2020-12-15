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
        'youtube': function (context) {
            var self = this, // ui has renders to build ui elements
                // for e.g. you can create a button with 'ui.button'
                ui = $.summernote.ui, $note = context.layoutInfo.note, // contentEditable element
                $body = $(document.body);
            $editor = context.layoutInfo.editor, $editable = context.layoutInfo.editable, $toolbar = context.layoutInfo.toolbar, // options holds the Options Information from Summernote and what we extended above.
                options = context.options, // lang holds the Language Information from Summernote and what we extended above.
                lang = options.langInfo;
            context.memo('button.youtube', function () {
                var button = ui.button({
                    contents: '<i class="fa fa-youtube"/>', tooltip: 'youtube', click: function (e) {
                        context.invoke('youtube.show');
                    }
                });
                return button.render();
            });
            this.initialize = function () {
                var $container = options.dialogsInBody ? $body : $editor;
                var body = ['<div class="form-group note-form-group row-fluid">', "<label class=\"note-form-label\">视频地址</label>", '<input class="note-video-url form-control note-form-control note-input" type="text" />', '</div>'].join('');
                var buttonClass = 'btn btn-primary note-btn note-btn-primary note-video-btn';
                var footer = "<input type=\"button\" href=\"#\" class=\"" + buttonClass + "\" value=\"" + lang.video.insert + "\" disabled>";
                this.$dialog = ui.dialog({
                    title: '插入 Youtube 视频', fade: ui.options.dialogsFade, body: body, footer: footer
                }).render().appendTo($container);
            };
            this.destroy = function () {
                ui.hideDialog(this.$dialog);
                this.$dialog.remove();
            };
            this.bindEnterKey = function ($input, $btn) {
                $input.on('keypress', function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        $btn.trigger('click');
                    }
                });
            };
            this.createVideoNode = function (url) {
                // video url patterns(youtube, instagram, vimeo, dailymotion, youku, mp4, ogg, webm)
                var ytRegExp = /\/\/(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w|-]{11})(?:(?:[\?&]t=)(\S+))?$/;
                var ytRegExpForStart = /^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/;
                var ytMatch = url.match(ytRegExp);
                var $video;
                var $url;
                var $img;
                if (ytMatch && ytMatch[1].length === 11) {
                    var youtubeId = ytMatch[1];
                    var start = 0;
                    if (typeof ytMatch[2] !== 'undefined') {
                        var ytMatchForStart = ytMatch[2].match(ytRegExpForStart);
                        if (ytMatchForStart) {
                            for (var n = [3600, 60, 1], i = 0, r = n.length; i < r; i++) {
                                start += (typeof ytMatchForStart[i + 1] !== 'undefined' ? n[i] * parseInt(ytMatchForStart[i + 1], 10) : 0);
                            }
                        }
                    }
                    $url = 'http://www.youtube.com/embed/' + youtubeId + (start > 0 ? '?start=' + start : '');
                    $img = $('<img>').attr('data-crawl', 'https://i.ytimg.com/vi/' + youtubeId + '/hqdefault.jpg').attr('src', 'https://i.ytimg.com/vi/' + youtubeId + '/hqdefault.jpg');
                    $video = $('<a>').attr('href', $url).append($img);
                    return $video[0];
                }
                return false;
            };
            this.show = function () {
                var text = context.invoke('editor.getSelectedText');
                context.invoke('editor.saveRange');
                this.showVideoDialog(text).then(function (url) {
                    // [workaround] hide dialog before restore range for IE range focus
                    ui.hideDialog(self.$dialog);
                    context.invoke('editor.restoreRange');
                    // build node
                    var $node = self.createVideoNode(url);
                    if ($node) {
                        // insert video node
                        context.invoke('editor.insertNode', $node);
                    }
                }).fail(function () {
                    context.invoke('editor.restoreRange');
                });
            };
            /**
             * show image dialog
             *
             * @param {jQuery} $dialog
             * @return {Promise}
             */
            this.showVideoDialog = function (text) {
                return $.Deferred(function (deferred) {
                    var $videoUrl = self.$dialog.find('.note-video-url');
                    var $videoBtn = self.$dialog.find('.note-video-btn');
                    ui.onDialogShown(self.$dialog, function () {
                        context.triggerEvent('dialog.shown');
                        $videoUrl.val(text).on('input', function () {
                            ui.toggleBtn($videoBtn, $videoUrl.val());
                        });
                        $videoUrl.trigger('focus');
                        $videoBtn.click(function (event) {
                            event.preventDefault();
                            deferred.resolve($videoUrl.val());
                        });
                        self.bindEnterKey($videoUrl, $videoBtn);
                    });
                    ui.onDialogHidden(self.$dialog, function () {
                        $videoUrl.off('input');
                        $videoBtn.off('click');
                        if (deferred.state() === 'pending') {
                            deferred.reject();
                        }
                    });
                    ui.showDialog(self.$dialog);
                });
            };
        }
    });
}));