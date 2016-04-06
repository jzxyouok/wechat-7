<?php

use modules\wechat\widgets\PagePanel;

$this->title = '修改粉丝';
$this->params['breadcrumbs'][] = ['label' => '粉丝列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $mpusermodel->nickname;
?>
<?php PagePanel::begin(['options' => ['class' => 'fans-update']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php PagePanel::end() ?>