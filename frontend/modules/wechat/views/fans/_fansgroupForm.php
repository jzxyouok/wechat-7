<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\FansGroups */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading ">
        <?php if ($model->getIsNewRecord()): ?>
            <b>新建分组</b>
        <?php else: ?>
            <b>修改分组</b>
        <?php endif ?>

    </div>
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
        ]); ?>
        <?= Html::hiddenInput('groupid', $model->groupid) ?>
        <?= $form->field($model, 'name')->textInput() ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('提交', ['class' => 'btn btn-block btn-primary','id'=>'fansgroup']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<script>
    $(document).ready(function(){
        $("#fansgroup").on("click",function(){
            var name=$('#fansgroups-name').val();
            var groupid=$("input[name='groupid']").val();

            <?php if ($model->getIsNewRecord()): ?>
            var url='<?= Url::to(['fans/create-fansgroup']); ?>';
            var data={'name':name};
            <?php else: ?>
            var url='<?= Url::to(['fans/update-fansgroup?id='.$model->id]); ?>';
            var data={'name':name,'id':groupid};
            <?php endif ?>

            $.post(url,data,function(data){
                if(data.status==1){
                    alert(data.info);
                    window.location.reload();
                }else{
                    alert(data.info);
                }
            },"json");
            return false;
        });
    });
</script>
