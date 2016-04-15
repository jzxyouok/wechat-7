<?php

use yii\helpers\Html;
use yii\grid\GridView;
use modules\wechat\models\Wechat;
use modules\wechat\widgets\PagePanel;

$this->title = '公众号列表';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php PagePanel::begin() ?>
    <p>
        <?= Html::a('添加公众号', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-hover'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
//        [
//            'class' => 'yii\grid\SerialColumn',
//            'header' => '序号'
//        ],
        [
            'attribute' => 'id',
            'options' => [
                'width' => 75
            ]
        ],
        'name',
//      'token',
//      'access_token',
//      'account',
        'original',
        [
            'attribute' => 'type',
            'format' => 'html',
            'value' => function($model) {
                return Html::tag('span', Wechat::$types[$model->type], [
                    'class' => 'label label-info'
                ]);
            },
            'filter' => Wechat::$types,

        ],
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function($model) {
                return Html::tag('span', Wechat::$statuses[$model->status], [
                    'class' => 'label label-' . ($model->status == Wechat::STATUS_ACTIVE ? 'success' : 'danger')
                ]);
            },
            'filter' => Wechat::$statuses,
        ],
//      'created_at:datetime',//默认格式
        [//此格式有排序功能
            'attribute' => 'created_at',
            'format' => ['date','php:Y-m-d H:i:s'],
        ],
//        [//此格式无排序功能
//            'format' => 'raw',
//            'label' => '创建时间',
//            'value' => function($m) {
//                     return date('Y-m-d H:i:s', $m->created_at);
//            }
//        ],
        [
            'attribute' => 'updated_at',
            'format' => ['date','php:Y-m-d H:i:s'],
        ],
//        'updated_at:datetime',
//        [
//            'format' => 'raw',
//            'label' => '修改时间',
//            'value' => function($m) {
//                return date('Y-m-d H:i:s', $m->updated_at);
//            }
//        ],
        [
            'class' => 'modules\wechat\widgets\ActionColumn',
            'template' => '{manage} {update} {delete}',
            'buttons' => [
                'manage' => function ($url, $model) {
                    return Html::a('管理此公众号', $url, [
                        'class' => 'text-danger',
                        'data' => [
                            'toggle' => 'tooltip',
                            'placement' => 'bottom'
                        ],
                        'title' => '管理此公众号'
                    ]);
                }
            ]
        ],
        //['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
<?php PagePanel::end() ?>
<?php
$this->registerJs(<<<EOF
    $('[data-toggle="tooltip"]').tooltip();
EOF
);