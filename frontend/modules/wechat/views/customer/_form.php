<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\widgets\ActiveForm;
use modules\wechat\widgets\FileApiInputWidget;
use modules\wechat\assets\FileApiAsset;

FileApiAsset::register($this);

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading ">
        <?php if ($model->getIsNewRecord()): ?>
            <b>新建客服</b>
        <?php else: ?>
            <b>修改客服</b>
        <?php endif ?>

    </div>
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => [
                'enctype' => "multipart/form-data"
            ]
        ]); ?>
        <?= Html::hiddenInput('id', $model->id) ?>
        <?= $form->field($model, 'kf_account')->textInput()->hint('格式为：账号前缀@公众号微信号') ?>
        <?= $form->field($model, 'kf_nick')->textInput() ?>
        <?php if (!$model->getIsNewRecord()): ?>
        <?= $form->field($model, 'kf_headimgurl')->widget(FileApiInputWidget::className(), [
            'jsOptions' => [
                'url' => Url::toRoute(['customer/ajax-upload','filename'=>'customavatar'])
            ]
        ]) ?>

            <div class="form-group" id="form_id" <?php if (!$model->kf_headimgurl): ?>style="display: none"<?php endif ?>>
                <?= '<label class="control-label col-sm-3">图片预览</label>'; ?>

                <?php $pos=strpos($model->kf_headimgurl,'http'); if ($pos === false): ?>
                    <?= '<div class="col-sm-6">'.Html::img('/'.$model->kf_headimgurl,['id'=>"img_id"]).'</div>'; ?>
                <?php else: ?>
                    <?= '<div class="col-sm-6">'.Html::img($model->kf_headimgurl,['id'=>"img_id"]).'</div>'; ?>
                <?php endif ?>

                <?= '<div class="help-block help-block-error col-sm-3"></div>'; ?>
            </div>

        <?php endif ?>
        <?php if ($model->kf_wx): ?>
            <?= $form->field($model, 'kf_wx')->textInput(['readonly'=>'true']) ?>
        <?php endif ?>
        <?php if ($model->invite_wx): ?>
        <?= $form->field($model, 'invite_wx')->textInput(['readonly'=>'true']) ?>
        <?php endif ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('提交', ['class' => 'btn btn-block btn-primary','id'=>'fansgroup']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
