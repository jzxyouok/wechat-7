<?php

use yii\helpers\Html;
use modules\wechat\models\Fans;
use modules\wechat\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\Fans */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fans-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'subscribe')->radioList(Fans::$subscribes) ?>

    <?= $form->field($model, 'created_at')->textInput([
        'value' => Yii::$app->formatter->asDatetime($model->created_at,'Y-M-d H:i:s'),
        'disabled' => true
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('提交', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
