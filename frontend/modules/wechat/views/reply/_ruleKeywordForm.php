<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
//use dosamigos\datetimepicker\DateTimePicker;

$prefix = '';
?>
<div class="panel <?= $model->getIsNewRecord() ? 'panel-info' : 'panel-default' ?>">
    <div class="panel-heading ">
        <?php if ($model->getIsNewRecord()): ?>
             <b>新建关键字</b>
        <?php else: ?>
            <b><?= Html::encode($model->keyword) ?></b>
        <?php endif ?>

    </div>
    <div class="panel-body">
        <?= Html::activeHiddenInput($model, "{$prefix}id") ?>
        <?= $form->field($model, "{$prefix}type")->dropDownList($model::$types) ?>
        <?= $form->field($model, "{$prefix}keyword")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}content")->textArea() ?>
        <?= $form->field($model, "{$prefix}start_at")->widget(
            DatePicker::className(), [
            // inline too, not bad
            'inline' => false,
            'language' => 'zh-CN' , //--设置为中文
            'template'=>'{input}{addon}',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
            'options' => [
                'value' =>$model->start_at?date('Y-m-d',$model->start_at):''
            ]
        ]);?>
        <?= $form->field($model, "{$prefix}end_at")->widget(
            DatePicker::className(), [
            // inline too, not bad
            'inline' => false,
            'language' => 'zh-CN' , //--设置为中文
            'template'=>'{input}{addon}',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
            'options' => [
                'value' => $model->end_at?date('Y-m-d',$model->end_at):''
            ]
        ]);?>
    </div>
</div>
