<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\redactor\widgets\Redactor;
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
    <div class="panel-body" style="width:750px">
        <?= Html::activeHiddenInput($model, "{$prefix}id") ?>
        <?= $form->field($model, "{$prefix}type")->dropDownList($model::$types) ?>
        <?= $form->field($model, "{$prefix}keyword")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}title")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}thumbs")->fileInput() ?>
        <?php if ($model->thumbs): ?>
            <?= Html::activeHiddenInput($model, "thumbs",['value'=>$model->thumbs]) ?>
        <div class="form-group">
            <?= '<label class="control-label col-sm-3">图片预览</label>'; ?>
            <?= '<div class="col-sm-6"><img src=/'.$model->thumbs.' style="height:200px;" /></div>'; ?>
            <?= '<div class="help-block help-block-error col-sm-3"></div>'; ?>
        </div>
        <?php endif ?>
        <?= $form->field($model, "{$prefix}descriptions")->textArea() ?>
        <?= $form->field($model, "{$prefix}content")->widget(Redactor::className(),
            [
                'clientOptions' => [
                    'imageManagerJson' => ['/redactor/upload/image-json'],
                    'imageUpload' => ['/redactor/upload/image'],
                    'fileUpload' => ['/redactor/upload/file'],
                    'lang' => 'zh_cn',
                    'plugins' => ['clips', 'fontcolor','imagemanager']
                ]
            ]
        ) ?>
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
