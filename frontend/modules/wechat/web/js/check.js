$(document).ready(function(){
    checkVideo();
});

//判断素材是否显示标题和描述输入框
function checkVideo(){
    var type = $('input:radio[name="MediaForm[type]"]:checked').val();
    var material = $('input:radio[name="MediaForm[material]"]:checked').val();

    var title = $('#mediaform-title').val();
    var introduction = $('#mediaform-introduction').val();

    if(type=='video'){
        if(material=='permanent'){
            $('#video_info').css('display','block');
        }else{
            $('#mediaform-title').attr("value","");
            $('#mediaform-introduction').attr("value","");
            $('#video_info').css('display','none');
            return;
        }
    }else{
        $('#mediaform-title').attr("value","");
        $('#mediaform-introduction').attr("value","");
        $('#video_info').css('display','none');
        return;
    }

    if(title!='' || introduction!=''){
        $('#video_info').css('display','block');
    }

}
