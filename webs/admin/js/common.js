/**
 * Created by Jony on 2017/7/16.
 */

$('[data-ajax=blur]').blur(function(){
    var val = $(this).val();
    var url = $(this).attr('data-ajax-url');
    var target = $(this).attr('data-ajax-target');

    if(target){
        target = $("#" + target);
    }else{
        target = $(this);
    }

    if(val){
        $.post(url, {value: val}, function(datas){
            if(datas.msg){
                if(datas.error){
                    toastr.warning(datas.msg);
                }else{
                    toastr.success(datas.msg);
                }
            }

            if(datas.result){
                if(target.prop("tagName").toLowerCase() == 'input'){
                    target.val(datas.result);
                }else{
                    target.text(datas.result);
                }
            }
        }, 'json');
    }
});