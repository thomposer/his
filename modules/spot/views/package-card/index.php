<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\modules\spot\models\PackageCard;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CardRechargeCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡中心';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '充值卡配置', 'url' => Url::to(['@spotCardManageGroupIndex'])],
        ['title' => '套餐卡配置', 'url' => Url::to(['@spotCardManagePackageCardIndex'])],
        ['title' => '服务卡管理', 'url' => Url::to(['@spotCardManageIndex'])],
    ],
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/overview/detail.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/cardCategory.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/packageCard.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class=" col-xs-12">
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box package-card-index delete_gap">
        <div class = 'row search-margin'>
            <div class = 'col-sm-4 col-md-4'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新建", Url::to(['@spotCardManagePackageCardCreate']), ['class' => 'btn btn-default', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-service-index', $this->params['permList'])): ?>
                    <?= Html::a("套餐卡服务类型管理", Url::to(['@spotCardManagePackageCardServiceIndex']), ['class' => 'btn btn-default package-card-service', 'data-pjax' => 0]) ?>
                <?php endif ?>
                
            </div>
            <div class = 'col-sm-8 col-md-8'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover header'],
            'headerRowOptions' => ['class' => 'header'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'name',
                ],
                [
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1 text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'price',
                ],
                [
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1 text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'validity_period',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'meta',
                ],
                [
                    'headerOptions' => ['class' => 'col-sm-5 col-md-5'],
                    'contentOptions' => ['class' => 'package-card-content'],
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'content',
                ],
                [
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'status',
                    'value' => function($model){
                        return PackageCard::$getStatus[$model->status];
                    }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{update}{update-status}',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-update', $this->params['permList'])) {
                                return Html::a('修改', [Url::to('@spotCardManagePackageCardUpdate'), 'id' => $model->id], ['data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                            } else {
                                return false;
                            }
                        },
                        'update-status' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-update-status', $this->params['permList'])) {
                                $options = [
                                        'data-confirm' => false, 
                                        'data-method' => false, 
                                        'data-toggle' => 'tooltip', 
                                        'role' => 'modal-remote', 
                                        'data-confirm-title' => '系统提示', 
                                        'data-pjax' => 0
                                        ];
                                if(1 == $model->status){
                                    $options['data-confirm-message'] = "确认停用吗？";
                                    return Html::a('停用', [Url::to('@spotCardManagePackageCardUpdateStatus'), 'id' => $model->id], $options);
                                }else{
                                    $options['data-confirm-message'] = '确认启用吗？';
                                    return Html::a('启用', [Url::to('@spotCardManagePackageCardUpdateStatus'), 'id' => $model->id], $options);
                                }
                            } else {
                                return false;
                            }
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
