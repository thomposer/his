<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use kartik\grid\GridView;
use app\modules\spot\models\Inspect;
use yii\helpers\Url;
use yii\helpers\Json;
//use kartik\grid\GridView as KGridView;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\InspectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '实验室检查管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/inspect.css') ?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/recipeList.css')?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="inspect-index col-xs-10">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="box">
        <div class='row search-margin'>
            <div class='col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/inspect-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spotChargeManageInspectCreate']), ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
                <?php endif ?>
            </div>
            <div class='col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel,'spotList' => $spotList]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
//            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table-border header'],
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
            'striped' => false,
            'condensed' => false,
            'hover' => true,
            'bordered' => false,
            /* 'filterModel' => $searchModel, */
            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'inspect_name',
                ],
                [
                    'attribute' => 'inspect_unit',
                ],
                [
                    'attribute' => 'phonetic',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'unionSpotId',
                    'contentOptions' => ['class' => 'configure-clinic-name'],
                    'value' => function($searchModel) use($spotNameList){
                        return $spotNameList[$searchModel->id]['spotName'];
                    }
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($searchModel) {
                        return Inspect::$getStatus[$searchModel->status];
                    },
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 '],
                    'template' => '{inspect-update}{inspect-delete}{relation}',
                    'buttons' => [
                        'inspect-update' => function ($url, $model) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-update', $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('修改', $url,['class'=>'op-group-a']);
                        },
                        'inspect-delete' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-delete', $this->params['permList'])) {
                                return false;
                            }

                            if ($model->status == 1) {
                                $title = '停用';
                                $deleteColor = false;
                                $message = "确认停用吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认停用后，该医嘱项在诊所下也会被停用并且医嘱套餐里该医嘱项也会被删除。</span>";
                            } else {
                                $title = '启用';
                                $deleteColor = true;
                                $message = '确认启用吗？';
                            }

                            $options = [
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => $deleteColor,
                                'data-confirm-message' => $message,
                                'class'=>'op-group-a'
                            ];
                            return Html::a($title, $url, $options);
                        },
                            'relation' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-union', $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'class' => 'op-group-a icon_relation j-modal3',
                                'record_id' => $model->id,
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'class'=>'op-group-a'
                            ];
                            return Html::a('关联项目', Url::to(['@spotChargeManageInspectUnion', 'id' => $model->id]), $options);
                        },
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
            <?php Pjax::end(); ?>
        </div>

        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>
        <script type="text/javascript">
            var baseUrl = '<?= $baseUrl ?>';
            var itemUrl = '<?= Url::to(['@spotChargeManageInspectItem']) ?>';
            var item_list = <?= Json::encode($item_list, JSON_ERROR_NONE) ?>;
            require(['<?= $baseUrl ?>' + '/public/js/spot/inspect.js?v=' + '<?= $versionNumber ?>'], function (main) {
                main.init();
            })
        </script>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
