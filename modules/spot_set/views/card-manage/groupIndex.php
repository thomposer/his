<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\modules\spot\models\CardRechargeCategory;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CardRechargeCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡中心';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/overview/detail.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/cardCategory.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class=" col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class = "box card-recharge-category-index delete_gap">
        <div class = 'row search-margin'>
            <div class = 'col-sm-4 col-md-4'>
            </div>
            <div class = 'col-sm-8 col-md-8'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover header'],
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
                    'class' => '\kartik\grid\ExpandRowColumn',
                    'defaultHeaderState' => 1,
                    'enableRowClick' => false,
                    'collapseIcon' => '<i class="fa fa-minus btn-box-tool"></i>',
                    'expandIcon' => '<i class="fa fa-plus btn-box-tool"></i>',
                    'detailUrl' => Url::to(['@spot_setCardManageSubclass']),
                    'value' => function ($model, $key, $index) {
                        return GridView::ROW_COLLAPSED;//配置默认展开或是收缩
                    }
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'contentOptions' => ['class' => 'spot_num_font'],
                    'attribute' => 'f_category_name',
//                    'format' => 'raw',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2', 'style' => "width:16"],
                    'contentOptions' => ['class' => 'spot_num'],
                    'attribute' => 'f_category_desc',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'attribute' => 'service',
                    'value' => function ($model) {
                        return '--';
                    }
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1 col-xs-1'],
                    'attribute' => 'f_state',
                    'value' => function ($model) {
                        return '--';
                }
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1 col-xs-1'],
                    'attribute' => 'f_update_time',
                    'format' => 'datetime'
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@spot_setCardManageGroupView'), $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'data-pjax' => 0
                            ];
                            /* 查看 */
                            return Html::a('查看', Url::to(['@spot_setCardManageGroupView', 'id' => $key]), $options);
                        },
                    ],
                ],
            ],
                    'striped' => false,
                    'condensed' => false,
                    'hover' => true,
                    'bordered' => false,
                ])
                ?>

            </div>
            <?php Pjax::end() ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
