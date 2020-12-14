/**
 * Created by Jony on 2017/1/12.
 */

var remSize = parseFloat($("html").css("font-size"), 10);
var popDialog;

function ajaxAction(datas){
    if(datas.html){
        layer.closeAll();
        popDialog = layer.open({
            type: 1,
            content: datas.html,
            style: 'width: 90%; border-radius: 0.2rem; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
        });
    }

    if(datas.msg){
        layer.open({
            content: datas.msg,
            btn: '我知道了'
        });
    }

    if(datas.url){
        window.location.href =  datas.url;
    }
}

function boxTextGo(obj, move){
    var i = parseInt(move / 50) * 1000;
    if(i < 3000){
        i = 3000;
    }
    $(obj).animate({marginLeft: '-' + move + 'px'}, i, function(){
        boxTextBack(obj, move);
    });
}

function boxTextBack(obj, move){
    var i = parseInt(move / 50) * 1000;
    if(i < 3000){
        i = 3000;
    }
    $(obj).animate({marginLeft: '0px'}, i, function(){
        boxTextGo(obj, move);
    });
}

$(document).on('click', '[ajax-page]', function(){
    var url = $(this).attr('ajax-page');

    $.get(url, function(datas){
        ajaxAction(datas);
    }, 'json');
});

$(document).on('click', '[ajax-form]', function(){
    var url = $(this).attr('ajax-form');
    var form = $(this).closest('form');

    $.post(url, form.serialize(), function(datas){
        ajaxAction(datas);
    }, 'json');
});

$(document).on('change', 'select[ajax-select]', function(){
    var select = $(this);
    var url = $(this).attr('ajax-select');
    var parentId = $(this).val();
    var inputName = $(this).attr('name');
    var form = select.closest('form');

    select.nextAll('select').remove();

    $.post(url, {_crsf: form.children('input[name="_csrf"]').val(), parentId: parentId, inputName: inputName}, function(datas){
        if(datas.html){
            select.after(datas.html);
        }
    }, 'json');
});

$(document).on('click', '#top-menu-toggle', function(){
    $('#top-menu-over').show();
    $('#top-menu').show();
});

$(document).on('click', '#top-menu-over', function(){
    $('#top-menu-over').hide();
    $('#top-menu').hide();
});


