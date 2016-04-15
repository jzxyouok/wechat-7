<?php
use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use yii\redactor\widgets\Redactor;
//use dosamigos\datetimepicker\DateTimePicker;
use modules\wechat\widgets\FileApiInputWidget;
use modules\wechat\assets\FileApiAsset;

FileApiAsset::register($this);
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
        <?= Html::activeHiddenInput($model, "id") ?>
        <?php if ($model->thumbs): ?>
            <?= Html::activeHiddenInput($model, "thumbs",['value'=>$model->thumbs]) ?>
        <?php endif ?>
        <?= $form->field($model, "type")->dropDownList($model::$types) ?>
        <?= $form->field($model, "keyword")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "title")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "thumbs")->widget(FileApiInputWidget::className(), [
            'jsOptions' => [
                'url' => Url::toRoute(['reply/ajax-upload','filename'=>'thumbs'])
            ]
        ]) ?>

        <div class="form-group" id="form_id" <?php if (!$model->thumbs): ?>style="display: none"<?php endif ?>>
            <?= '<label class="control-label col-sm-3">图片预览</label>'; ?>

            <?= '<div class="col-sm-6">'.Html::img('/'.$model->thumbs,['id'=>"img_id"]).'</div>'; ?>

            <?= '<div class="help-block help-block-error col-sm-3"></div>'; ?>
        </div>

        <?= $form->field($model, "descriptions")->textArea() ?>
        <?= $form->field($model, "content")->widget(Redactor::className(),
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

        <?= $form->field($model, "url")->textInput() ?>
        <?= $form->field($model, "start_at")->widget(
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
        <?= $form->field($model, "end_at")->widget(
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
