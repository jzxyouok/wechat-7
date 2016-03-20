<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use modules\wechat\models\ReplyRule;
use modules\wechat\widgets\ActiveForm;
?>

<div class="reply-rule-form">

    <?php if (!empty(Yii::$app->params['type'])): ?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
    <?php else: ?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal'
        ]); ?>
    <?php endif ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(ReplyRule::$statuses,['prompt' => '请选择']) ?>

    <?= $form->field($model, 'processor')->textInput(['maxlength' => true, 'placeholder' => ReplyRule::PROCESSOR_DEFAULT]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true, 'placeholder' => 0]) ?>

    <?php if (!empty(Yii::$app->params['type'])): ?>
        <?= Html::activeHiddenInput($model, "module",['value'=>strtolower(Yii::$app->params['type'])]) ?>
    <?php else: ?>
        <?= Html::activeHiddenInput($model, "module",['value'=>'text']) ?>
    <?php endif ?>


    <div class="form-group">
        <label class="control-label col-sm-3">触发关键字</label>
        <div class="col-sm-6" <?php if (!empty(Yii::$app->params['type'])): ?>style='width:650px'<?php endif ?>>
            <?php if (!empty($ruleKeywords)): ?>
                <?php foreach ($ruleKeywords as $index => $_ruleKeyword): ?>
                    <?= $this->render('_ruleKeywordForm'.Yii::$app->params['type'], [
                        'form' => $form,
                        'index' => $index,
                        'model' => $_ruleKeyword
                    ])?>
                <?php endforeach ?>
            <?php endif ?>

            <?php if ($model->getIsNewRecord()): ?>
                <?= $this->render('_ruleKeywordForm'.Yii::$app->params['type'], [
                    'form' => $form,
                    'model' => $ruleKeyword
                ]) ?>
            <?php endif ?>

        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '创建回复规则' : '修改回复规则', [
                'class' => 'btn btn-block ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
