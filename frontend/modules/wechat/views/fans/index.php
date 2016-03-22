<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\models\Fans;
use modules\wechat\widgets\GridView;
use modules\wechat\widgets\PagePanel;
use modules\wechat\assets\WechatAsset;

$wechatAsset = WechatAsset::register($this);

$this->title = '粉丝列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'fans-update']]) ?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'options' => [
                'width' => 75
            ]
        ],
        [
            'attribute' => 'user.avatar',
            'format' => 'html',
            'value' => function ($model) use ($wechatAsset) {
                return Html::img($model->user ? $model->user->avatar : $wechatAsset->baseUrl . '/images/anonymous_avatar.jpg', [
                    'width' => 40,
                    'class' => 'img-rounded'
                ]);
            },
            'options' => [
                'width' => 70
            ]
        ],
        [
            'attribute' => 'user.nickname',
            'value' => function ($model) {
                return $model->user ? $model->user->nickname : '';
            }
        ],
        [
            'attribute' => 'open_id',
            'options' => [
                'width' => 200
            ]
        ],
        [
            'attribute' => 'subscribe',
            'format' => 'html',
            'value' => function($model) {
                return Html::tag('span', Fans::$subscribes[$model->subscribe], [
                    'class' => 'label label-' . ($model->subscribe == Fans::STATUS_SUBSCRIBED ? 'success' : 'info')
                ]);
            },
            'filter' => Fans::$subscribes,
            'options' => [
                'width' => 120
            ]
        ],
        [
            'attribute' => 'user.subscribe_time',
            'format' => ['date','php:Y-m-d H:i:s'],
        ],
        [
            'class' => 'modules\wechat\widgets\ActionColumn',
            'header' => '操作',
            'template' => '{message} {update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('更新', $url, [
                        'title' => Yii::t('app', '更新'),
                    ]);
                },
                'message' => function ($url, $model, $key) {
                    return Html::a('发送消息',$url, [
                        'title' => Yii::t('app', '更新'),
                    ]);
                }
            ],
        ],
    ],
]); ?>

<button type="button" onclick="checkFans();" class="btn btn-primary">同步粉丝</button>
<?php PagePanel::end() ?>
<script type="text/javascript">
    function checkFans(){
        $.post('<? echo Url::to(['fans/check-fans']); ?>',
            function (data) {
                if(data.status==1){
                    alert(data.info);
                    window.location.reload();
                }else{
                    alert(data.info);
                }

            },
            "json")
    }
</script>
