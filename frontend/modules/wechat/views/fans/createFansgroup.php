<?php
Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
    <?= $this->render('_fansgroupForm', [
        'model' => $model,
    ]) ?>
