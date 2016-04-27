<?php
use yii\helpers\Html;
use modules\wechat\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<div class="panel panel-info">
    <div class="panel-heading ">
       <b>发送模板信息</b>

    </div>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>
    <div class="panel-body">
        <?= Html::activeHiddenInput($model, "open_id",['value'=>$model->open_id]) ?>
        <div class="form-group field-fansgroups-name required">
            <label for="fansgroups-name" class="control-label col-sm-3">模板编号</label>
            <div class="col-sm-6">
                <input type="text" value="" name="template_id_short" class="form-control" id="template_id_short">
            </div>
            <div class="help-block help-block-error col-sm-3"></div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('发送', [
                'class' => 'btn btn-block btn-primary'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
