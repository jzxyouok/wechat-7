<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use modules\wechat\widgets\GridView;
use modules\wechat\assets\FileApiAsset;
use modules\wechat\assets\AudioAsset;
use modules\wechat\models\Media;

FileApiAsset::register($this);
AudioAsset::register($this);
Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>
<style>
    .audiojs{
        width: 150px;
    }
    .audiojs .scrubber{
        width: 90px;
    }
    .audiojs .error-message{
        width: 150px;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="exampleModalLabel">选择媒体素材</h4>
</div>
<div class="modal-body">
    <div class="grid-view" id="w0">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>媒体类型</th>
                <th>素材类别</th>
                <th>创建时间</th>
                <th>文件名</th>
                <th class="action-column">操作</th>
            </tr>
            </thead>
            <tbody>
    <?php
    foreach($model as $key=>$val)
    {
        $str='';
        if($val->material=='tomporary' && $val->created_at+259200>time()){
            $str.='<tr data-key="'.$val->id.'">
                <td>'.$val->id.'</td>
                <td>'.(new Media())->getByType($val->type).'</td>
                <td>'.(new Media())->getByMaterial($val->material).'</td>
                <td>'.date('Y-m-d H:i:s',$val->created_at).'</td>
                <td>
                    '.(new Media())->getByFile($val->type,$val->file).'
                </td>
                <td><a class="btn btn-default btn-xs" href="javascript:void(0)" onclick="clickToMedia(\''.$val->mediaId.'\',\''.$val->type.'\')">选中</a>
                </td>
            </tr>';
        }else if($val->material=='permanent'){
            $str.='<tr data-key="'.$val->id.'">
                <td>'.$val->id.'</td>
                <td>'.(new Media())->getByType($val->type).'</td>
                <td>'.(new Media())->getByMaterial($val->material).'</td>
                <td>'.date('Y-m-d H:i:s',$val->created_at).'</td>
                <td>
                    '.(new Media())->getByFile($val->type,$val->file).'
                </td>
                <td><a class="btn btn-default btn-xs" href="javascript:void(0)" onclick="clickToMedia(\''.$val->mediaId.'\',\''.$val->type.'\')">选中</a>
                </td>
            </tr>';
        }
        echo $str;

    }
    ?>
            </tbody>
        </table>
    </div>
    <?= LinkPager::widget(['pagination' =>$pages,'linkOptions'=>['onclick'=>"pageAjax(this);return false;"]]); ?>
</div>
<div class="modal-footer">
    <?= Html::a('上传文件', ['create'], [
        'class' => 'pull-left btn btn-success'
    ]) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="button" class="btn btn-primary">确定</button>
</div>
<script>
    /**
     * 根据不同类型选择素材
     * @data 素材mediaid
     * @type 素材类型
     */
    function clickToMedia(data,type){
        if(type=='thumb'){
            $("#message-thumbmediaid").val(data);
        }else{
            $("#message-mediaid").val(data);
        }
        $('#mediaModal').modal('hide');
    }

    function pageAjax(a){
        var data=$(a).attr('href');

        $.post('<? echo Url::to(['media/pick']); ?>',{page:data},
            function (data) {
                $('.modal-content').html(data);
            },
            "html")
    }
</script>