<?php

use yii\grid\GridView;
use app\assets\AppAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\overview\models\Search\OverviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$baseUrl = Yii::$app->request->baseUrl;
?>
  
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
//             'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover'],
            'layout' => '{items}',
            'showHeader' => false,
//             'headerRowOptions' => ['hidden' => true],
            /* 'filterModel' => $searchModel, */
            'columns' => [
                [
                    'attribute' => 'spot_name',
                    'contentOptions' => ['style' => 'padding-left:60px']

                ],
                [
                    'attribute' => 'spot_num',

                ],
                [
                    'attribute' => 'contact_name',
                    'contentOptions' => ['style' => 'padding-left:19%','class'=>'col-md-3']
                ],
                [
                    'attribute' => 'contact_iphone',
                    'contentOptions' => ['style' => 'padding-left:6.3%']
                ],
                [
                    'attribute' => 'create_time',
                    'format' => 'date',
                    'contentOptions' => ['style' => 'padding-left:3.5%']
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'contentOptions' => ['class' => 'op-group'],
                    'template' => '{spot-view}{spot-go}',
                    'buttons' => [
                        'spot-view' => function($url,$model,$key){
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@overviewIndexSpotView'), $this->params['permList'])) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ]);
                            /*fa-eye是查看*/
                            return Html::a('<span class="icon_button_view fa fa-eye" title="查看", data-toggle="tooltip"></span>', $url, $options);
                        },
                        'spot-go' => function($url,$model,$key){
                            if (!isset($this->params['permList']['role'])) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', '进入诊所'),
                                'aria-label' => Yii::t('yii', '进入诊所'),
                                'data-pjax' => '0',
                                'id' => $key,
                                'class' => 'spot-go',
                            ]);
                            /*fa-eye是查看*/
                            return Html::a('<span class="icon_button_view fa fa-arrow-right" title="进入诊所", data-toggle="tooltip"></span>','javascript:void(0);', $options);
                        }
                    ]
                ],
            ],
        ]);
        ?>
