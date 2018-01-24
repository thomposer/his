<?php
use yii\grid\GridView;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\charge\models\ChargeRecord;
use yii\helpers\Html;
?>
<div class="charge-record-log-form">
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover  add-table-border'],
                'headerRowOptions' => ['class' => 'header'],
                'layout' => '{items}<div class="text-right">{pager}</div>',
                'pager' => [
                    //'options'=>['class'=>'hidden']//关闭自带分页

                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => [
                    [
                        'attribute' => 'create_time',
                        'headerOptions' => ['class' => 'table-time-width'],
                        'value' => function ($model) {
                            return date('Y-m-d', $model->create_time);
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return ChargeRecordLog::$getType[$model->type];
                        }
                    ],
                    [
                        'attribute' => '支付方式',
                        'value' => function($model){
                            return ChargeRecord::$getType[$model->pay_type];
                        }
                    ],
                    [
                        'attribute' => 'price',
                        'value'=>function($model){
                            if($model->price != '0.00'){
                                $price = $model->type == 1?'+'.$model->price:'<span style="color:red;">-'.$model->price.'</span>';
                            }else{
                                $price = $model->price;
                            }
                            return $price;
                        },
                        'format'=>'raw'
                    ],
                    [
                        'class' => 'app\common\component\ActionColumn',
                        'template' => "{view}",
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@chargeIndexTradeLog'), $this->params['permList'])) {
                                    return false;
                                }

                                $options = array_merge([
                                    'data-pjax' => '0',
                                    'target' => '_blank',
                                    'class' => ' op-group-a',
                                ]);
                                return Html::a('查看', ['@chargeIndexTradeLog', 'id' => $model->id], $options);
                            },
                        ]
                    ],
                ],
            ])
            ?>
        </div>
    </div>
</div>
