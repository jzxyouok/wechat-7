<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
//use dosamigos\datetimepicker\DateTimePicker;
use modules\wechat\assets\AudioAsset;

AudioAsset::register($this);
$prefix = '';
?>
<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>
<style>
    .audiojs{
        width: 345px;
    }
    .audiojs .scrubber{
        width: 150px;
    }
    .audiojs .error-message{
        width: 280px;
    }
</style>
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
        <?= $form->field($model, "{$prefix}music")->fileInput() ?>
        <?php if ($model->music): ?>
            <?= Html::activeHiddenInput($model, "music",['value'=>$model->music]) ?>
            <div class="form-group">
                <?= '<label class="control-label col-sm-3">音乐预览</label>'; ?>
                <?= '<div class="col-sm-6"><audio src="/'.$model->music.'" preload="auto" /></audio></div>'; ?>
                <?= '<div class="help-block help-block-error"><a href="javascript:void(0)" onclick="if(confirm(\'确定删除该条音乐记录?\')){delMusic('.$model->id.','."'music'".');}">删除音乐</a></div>'; ?>
            </div>
        <?php endif ?>
        <?= $form->field($model, "{$prefix}HQMusic")->fileInput() ?>
        <?php if ($model->HQMusic): ?>
            <?= Html::activeHiddenInput($model, "HQMusic",['value'=>$model->HQMusic]) ?>
            <div class="form-group">
                <?= '<label class="control-label col-sm-3">高质量音乐预览</label>'; ?>
                <?= '<div class="col-sm-6"><audio src="/'.$model->HQMusic.'" preload="auto" /></audio></div>'; ?>
                <?= '<div class="help-block help-block-error"><a href="javascript:void(0)" onclick="if(confirm(\'确定删除该条音乐记录?\')){delMusic('.$model->id.','."'HQMusic'".');}">删除音乐</a></div>'; ?>
            </div>
        <?php endif ?>
        <?= $form->field($model, "{$prefix}descriptions")->textArea() ?>
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
    <script>
        function delMusic(id,type){
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl. '/wechat/reply/delete-music' ?>',
                type: 'get',
                dataType: 'json',
                data: {id: id,type:type},
                success: function (data) {
                    alert(data.info);
                    window.location.reload();
                }
            });
        }
    </script>
</div>
