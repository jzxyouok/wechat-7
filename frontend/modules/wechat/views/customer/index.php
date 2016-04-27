<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\widgets\GridView;
use modules\wechat\widgets\PagePanel;

$this->title = '多客服管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'media-index']]) ?>
    <p>
        <?= Html::a('添加客服', ['create'], [
            'class' => 'btn btn-success',
            'data' => [
                'toggle' => 'modal',
                'target' => '#customerCreateModal'
            ]
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $customerModel,
        'columns' => [
            'id',
            [
                'attribute' => 'kf_account',
            ],
            [
                'attribute' => 'kf_nick',
                'format' => 'html',
                'value' => function($model) {
                    $notic='';
                    if(!$model->kf_wx){
                        $notic=' <b>[未绑定微信]</b>';
                    }
                    return Html::tag('span', $model->kf_nick.$notic);
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['date','php:Y-m-d H:i:s'],
            ],
            [
                'class' => 'modules\wechat\widgets\ActionColumn',
                'template' => '{invite} {update} {delete}',
                'header' => '操作',
//                'headerOptions' => ['width' => '80px'],
//                'contentOptions' => ['style' => 'padding-left:5px'],
                'options' => [
                    'width' => 200
                ],
                'buttons' => [
                    'invite' => function($url, $model, $key){
                        return Html::a('<i class="fa fa-file"></i> 绑定微信',
                            [Yii::$app->params['inviteUrl'], 'id' => $key],
                            ['class' => 'btn btn-default btn-xs','data'=> [
                                'toggle' => 'modal',
                                'target' => '#inviteModal'
                            ]
                        ]);
                    },
                    'update' => function($url, $model, $key){
                            return Html::a('<i class="fa fa-file"></i> 更新',
                                [Yii::$app->params['editUrl'], 'id' => $key],
                                ['class' => 'btn btn-default btn-xs']);
                    },
                    'delete' => function($url, $model, $key){
                            return Html::a('<i class="fa fa-ban"></i> 删除',
                                [Yii::$app->params['delUrl'], 'id' => $key],
                                [
                                    'class' => 'btn btn-default btn-xs',
                                    'data' => ['confirm' => '你确定要删除吗？',]
                                ]
                            );
                    },

                ],
            ],
        ],
    ]); ?>

<button type="button" onclick="checkCustomer();" class="btn btn-primary">同步客服</button>
<button type="button" onclick="window.open('https://mpkf.weixin.qq.com/','_blank');" class="btn btn-primary">登录客服聊天系统</button>
<?php PagePanel::end() ?>
<script type="text/javascript">
    function checkCustomer(){
        $.post('<? echo Url::to(['customer/check-customer']); ?>',
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

