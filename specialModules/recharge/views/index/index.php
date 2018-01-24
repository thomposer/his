<?php

use app\modules\user\models\User;
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\common\Common;
use app\specialModules\recharge\models\CardRecharge;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\specialModules\recharge\models\search\CardRechargeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员卡';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/card/index.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<?php $this->registerCss("
    .single-line {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        word-break: break-all;
        overflow: hidden;
        float: left;
        width: 92px;
    }
");
?>


<div class="card-recharge-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render('@cardTopTabViewPath'); ?>
    <div class="box delete_gap">
        <div class='row search-margin'>
            <div class='col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'middle', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/export-flow', $this->params['permList'])): ?>
                    <?= Html::a("导出", ['export-flow'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
                <?php endif; ?>
            </div>
            <div class='col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
                'f_user_name',
                'f_phone',
                'f_baby_name',
                [
                    'attribute' => 'f_sale_id',
                    'value' => function($dataProvider) {
                        return User::getUserInfo($dataProvider->f_sale_id, 'username')["username"];
                    }
                ],
                'category_name',
                [
                    'attribute' => 'f_card_fee',
                    'contentOptions' => ['class' => 'text-right'],
                    'headerOptions' => ['class' => 'text-right'],
                    'value' => function ($dataProvider) {
                           return Common::num(($dataProvider->f_card_fee + $dataProvider->f_donation_fee)) ;
                    },
                ],
                [
                    'attribute' => 'f_buy_time',
                    'value' => function ($dataProvider) {
                        if ($dataProvider->f_buy_time) {
                            return date("Y-m-d H:i", $dataProvider->f_buy_time);
                        } else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'f_is_logout',
                    'format' => 'raw',
                    'value' => function ($dataProvider) {
                        return 0 == $dataProvider->f_is_logout ? CardRecharge::$getIsLogout[$dataProvider->f_is_logout] : "<span class='red'>" . CardRecharge::$getIsLogout[$dataProvider->f_is_logout] . "</span>";
                    },
                ],
                // 'f_order_status',
                // 'f_pay_type',
                // 'f_state',
                // 'f_property',
                // 'f_create_time',
                // 'f_update_time',
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{preview}{flow}{record}{logout}',
                    'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3'],
                    'buttons' => [
                        'preview' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/preview', $this->params['permList'])) {
                                return Html::a('查看', ['preview', 'id' => $model->f_physical_id], ['class' => 'a-card', 'data-toggle' => 'tooltip', 'data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                            } else {
                                return false;
                            }
                        },
                                'flow' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/flow', $this->params['permList'])) {
                                return Html::a('流水', ['flow', 'id' => $model->f_physical_id, 'type' => 2], ['class' => 'a-card', 'data-toggle' => 'tooltip', 'data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                            } else {
                                return false;
                            }
                        },
                                'record' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/recharge', $this->params['permList'])) {
                                if (0 == $model->f_is_logout) {
                                    return Html::a('充值', ['recharge', 'id' => $model->f_physical_id], ['class' => 'a-card', 'role' => 'modal-remote', 'data-toggle' => 'tooltip','data-modal-size' => 'middle','data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                                } else {
                                    return Html::a('充值', null, ['style' => 'color:#CACFD8']) . '<span style="color:#99a3b1">丨</span>';
                                }
                            } else {
                                return false;
                            }
                        },
                                'logout' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/logout', $this->params['permList'])) {
                                if (0 == $model->f_is_logout) {
                                    return Html::a('注销', ['logout', 'id' => $model->f_physical_id], ['class' => 'a-card', 'data-confirm' => false, 'data-method' => false, 'data-confirm-message' => '确定要注销卡片吗？注销后不可恢复，但仍可以查看原卡片信息、交易流水', 'data-toggle' => 'tooltip', 'role' => 'modal-remote', 'data-confirm-title' => '系统提示', 'data-pjax' => 0]);
                                } else {
                                    return Html::a('已注销', null, ['style' => 'color:#CACFD8']);
                                }
                            } else {
                                return false;
                            }
                        },
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
            <?php Pjax::end() ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
