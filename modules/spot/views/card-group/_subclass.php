<?php

use yii\grid\GridView;
use app\assets\AppAsset;
use yii\helpers\Html;
use app\modules\spot\models\CardRechargeCategory;
use yii\helpers\Url;
use app\modules\spot\models\CardDiscount;

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
        $cardDiscountList = CardDiscount::getCardTagDiscount($model->f_physical_id);
        foreach ($cardDiscountList as $v) {
            $text[] = $v['name'];
        }
//         $model->f_inspect_discount != null && $text[] = '实验室检查' . $model->f_inspect_discount . '%';
//         $model->f_check_discount != null && $text[] = '影像学检查' . $model->f_check_discount . '%';
//         $model->f_cure_discount != null && $text[] = '治疗' . $model->f_cure_discount . '%';
//         $model->f_recipe_discount != null && $text[] = '药品' . $model->f_recipe_discount . '%';
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
            'class' => 'app\common\component\ActionTextColumn',
            'contentOptions' => ['style' => 'padding-left:2%', 'class' => 'op-group'],
            'template' => '{view}{operation}',
            'buttons' => [
                'view' => function($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@spotCardManageCategoryView'), $this->params['permList'])) {
                        return false;
                    }
                    $options = array_merge([
                        'class' => 'op-group-a',
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ]);
                    /* fa-eye是查看 */
                    return Html::a('查看', Url::to(['@spotCardManageCategoryView', 'id' => $key]), $options);
                },
                        'operation' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@spotCardManageCategoryOperation'), $this->params['permList'])) {
                        return false;
                    }
                    $stateTextArr = [1 => '启用', 2 => '停用', 3 => '启用'];
                    if ($model->f_state == 2) {
                        $text = '确定停止发行吗?停止发行后诊所下的充值卡也会停止发行。';
                        $dataDelete = false;
                    } else {
                        $text = '确定发行吗?';
                        $dataDelete = true;
                    }
                    $options = [
                        'class' => 'op-group-a',
                        'title' => $stateTextArr[$model->f_state],
                        'data-confirm' => false,
                        'data-method' => false,
                        'data-request-method' => 'post',
                        'role' => 'modal-remote',
                        'data-confirm-title' => '系统提示',
                        'data-delete' => $dataDelete,
                        'data-confirm-message' => $text,
                    ];
                    /* 查看 */
                    return Html::a($stateTextArr[$model->f_state], Url::to(['@spotCardManageCategoryOperation', 'id' => $key]), $options);
                }
                    ]
                ],
            ],
        ]);
        ?>
