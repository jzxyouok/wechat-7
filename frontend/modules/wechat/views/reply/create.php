<?php

use yii\helpers\Html;
use modules\wechat\widgets\PagePanel;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => [$url]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'reply-create']]) ?>

<?= $this->render($formDisplay, [
    'model' => $model,
    'ruleKeyword' => $ruleKeyword,
    'ruleKeywords' => $ruleKeywords,
    'type' => Yii::$app->params['type'],
]) ?>

<?php PagePanel::end() ?>
