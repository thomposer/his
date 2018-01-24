<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\spot_set\models\InspectClinic;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\search\InspectClinicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '实验室检查医嘱';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="inspect-clinic-index col-xs-10">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/inspect-clinic-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spot_setChargeManageInspectClinicCreate']), ['class' => 'btn btn-default font-body2', 'data-pjax' => 0, 'role' => 'modal-remote', 'data-modal-size' => 'large']) ?>
                <?php endif ?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
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
                'id',
                'attribute' => 'inspectName',
                [
                   'attribute' => 'deliver_organization',
                    'label'=>'是否外送',
                    'value' => function($searchModel) {
                        if($searchModel->deliver==1){
                            $text=InspectClinic::$getDeliverOrganization[$searchModel->deliver_organization];
                        }else{
                            $text='否';
                        }
                        return $text;
                    }
                ],
                'inspect_price',
                'phonetic',
                'doctorRemark',
                [
                    'attribute' => 'status',
                    'value' => function($searchModel) {
                        return InspectClinic::$getStatus[$searchModel->status];
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 '],
                    'updateOptions' => ['data-modal-size' => 'large'],
                    'ajaxList' => ['update' => true, 'delete' => true],
                    'template' => '{inspect-clinic-update}{inspect-clinic-delete}{union}',
                    'buttons' => [
                        'inspect-clinic-update' => function($url, $model, $key){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-clinic-update', $this->params['permList'])) {
                                return false;
                            }
                            $options = array_merge([
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large'
                            ]);
                            return Html::a('修改', $url, $options);
                        },
                       'inspect-clinic-delete' => function ($url, $model, $key) {
                           if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-clinic-delete', $this->params['permList'])) {
                               return false;
                           }
                           $message ="确认删除吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认删除后，医嘱套餐里该医嘱项也会被删除。</span>";
                           $options = [
                              'data-pjax' => 0,
                              'role' => 'modal-remote',
                              'data-request-method' => 'post',
                              'data-confirm-title'=> '系统提示',
                              'data-confirm-message'=> $message
                           ];
                           return Html::a('删除', $url, $options);
                       },
                        'union' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/inspect-clinic-union', $this->params['permList'])) {
                                return false;
                            }
                            if ($model->status == 2) {
                                return false;
                            }
                            $options = [
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large'
                            ];
                            return Html::a('关联项目', Url::to(['@spot_setChargeManageInspectClinicUnion', 'id' => $model->id]), $options);
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
        <?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js') ?>
        <?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js') ?>
        <script type="text/javascript">
            var baseUrl = '<?= $baseUrl ?>';
            var inspectList = <?= json_encode($inspectList, JSON_ERROR_NONE) ?>;
            var inspectStatus = <?= json_encode(InspectClinic::$getStatus, JSON_ERROR_NONE) ?>;
            require(['<?= $baseUrl ?>' + '/public/js/spot_set/inspectClinic.js?v=' + '<?= $versionNumber ?>'], function (main) {
                main.init();
            })
        </script>
        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
