<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\common\Common;
use app\modules\patient\models\Patient;
use app\specialModules\recharge\models\MembershipPackageCard;
use yii\helpers\Url;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\specialModules\recharge\models\search\CardRechargeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '套餐卡';
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


<div class="package-card-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render('@cardTopTabViewPath'); ?>
    <div class="box delete_gap">
        <div class='row search-margin'>
            <div class='col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create-package-card', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['create-package-card','step' => 1], ['class' => 'btn btn-default font-body2', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'middle', 'data-pjax' => 0]) ?>
                <?php endif ?>
            </div>
            <div class='col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel,'packageCardList' => $packageCardList]); ?>
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
                [
                    'attribute' => 'patientInfo',
                    'value' => function ($searchModel) {
                        $birth = Patient::dateDiffage($searchModel->birthday, time());
                        $text = Html::encode($searchModel->username) . '( ' . Patient::$getSex[$searchModel->sex] . ' ' . $birth . ' )';
                        return $text;
                    },
                    'format' => 'raw',
                ],
                'iphone',
                'name',
                [
                    'attribute' => 'active_time',
                    'format' => 'datetime',
                ],
                [
                    'attribute' => 'validity_period',
                    'value' => function($searchModel){
                        $year = date('Y',$searchModel->create_time);
                        $time = date('m-d H:i:s',$searchModel->create_time);
                        $vidateTime = ($year + $searchModel->validity_period) . '-'.$time;
                        return $vidateTime;
                    },
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function($searchModel){
                        $year = date('Y',$searchModel->create_time);
                        $time = date('m-d H:i:s',$searchModel->create_time);
                        $vidateTime = ($year + $searchModel->validity_period) . '-'.$time;
                        if(time() > strtotime($vidateTime) ){
                            return MembershipPackageCard::$cardStatus[2];
                        }
                        return MembershipPackageCard::$cardStatus[$searchModel->status];
                    },
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}{delete}',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-view', $this->params['permList'])) {
                                return Html::a('查看', ['package-card-view', 'id' => $model->id], ['data-pjax' => 0,'style' => 'margin-right:10px']);
                            } else {
                                return false;
                            }
                        },
                        'delete' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/package-card-delete', $this->params['permList'])) {
                                return false;
                        }
                            $deleteStr = $model->status == 1?'确认停用吗？':'确认启用吗？';
                            $options = [
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => false,
                                'data-modal-size'=>'middle',
                                'data-confirm-message' => $deleteStr,
                            ];
                            return Html::a($model->status==1?'停用':'启用', Url::to(['@rechargeIndexPackageCardDelete', 'id' => $model->id]), $options);
                        }
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
