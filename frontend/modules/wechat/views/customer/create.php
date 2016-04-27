<?php

$this->title = '添加客服';
Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
