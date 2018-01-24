<?php

use yii\grid\GridView;
use app\assets\AppAsset;
use yii\helpers\Html;
use app\modules\spot\models\CardRechargeCategory;
use yii\helpers\Url;
use app\modules\spot_set\models\CardDiscountClinic;

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
            'attribute' => 'f_category_name',
            'contentOptions' => ['style' => 'padding-left:2.5%']
        ],
        [
            'contentOptions' => ['style' => 'padding-left:5.5%'],
            'attribute' => 'f_category_desc',
        ],
        [
            'attribute' => 'service',
            'contentOptions' => ['style' => 'padding-left:3.5%'],
            'value' => function ($model) {
                $text = [];
                $cardDiscountList =CardDiscountClinic::cardDiscountListClinic($model->f_physical_id);
                foreach ($cardDiscountList as $v){
                    $text[] = $v['name'].$v['discount'].'%';
                }
                return implode('，', $text);
            }
        ],
        [
            'attribute' => 'f_state',
            'contentOptions' => ['style' => 'padding-left:9.5%'],
            'value' => function ($model) {
                return CardRechargeCategory::$getState[$model->f_state];
            }
        ],
        [
            'attribute' => 'f_update_time',
            'contentOptions' => ['class' => 'col-sm-1 col-md-1 col-xs-1', 'style' => 'padding-left:1%'],
            'format' => 'datetime',
        ],
        [
            'class' => 'app\common\component\ActionColumn',
            'contentOptions' => ['style' => 'padding-left:2%', 'class' => 'op-group'],
            'template' => '{view}',
            'buttons' => [
                'view' => function($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@spot_setCardManageCategoryView'), $this->params['permList'])) {
                        return false;
                    }
                    $options = array_merge([
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ]);
                    /* fa-eye是查看 */
                    return Html::a('查看', Url::to(['@spot_setCardManageCategoryView', 'id' => $key]), $options);
                },
            ]
        ],
            ],
        ]);
        ?>
