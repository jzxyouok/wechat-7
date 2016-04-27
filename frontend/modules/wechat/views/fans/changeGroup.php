<?php
use yii\helpers\Html;
use modules\wechat\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<div class="panel <?= $model->getIsNewRecord() ? 'panel-info' : 'panel-default' ?>">
    <div class="panel-heading ">
       <b>移动用户分组</b>

    </div>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>
    <div class="panel-body">
        <?= Html::activeHiddenInput($model, "open_id",['value'=>$model->open_id]) ?>
        <?= $form->field($model, "group_id")->dropDownList(ArrayHelper::map($fansgoup,'groupid','name')) ?>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('移动', [
                'class' => 'btn btn-block btn-primary'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
