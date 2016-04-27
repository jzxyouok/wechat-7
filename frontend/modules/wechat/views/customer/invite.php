<?php
use yii\helpers\Html;
use modules\wechat\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<div class="panel <?= $model->getIsNewRecord() ? 'panel-info' : 'panel-default' ?>">
    <div class="panel-heading ">
       <b>绑定微信</b>

    </div>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>
    <div class="panel-body">
        <?= Html::activeHiddenInput($model, "kf_account",['value'=>$model->kf_account]) ?>
        <?= $form->field($model, "kf_wx")->textInput() ?>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('绑定', [
                'class' => 'btn btn-block btn-primary'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
