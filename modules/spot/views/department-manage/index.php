<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\helpers\Url;
CrudAsset::register($this);

$this->title = '科室管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset::addCss($this, '@web/public/css/overview/detail.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/departmentManage.css') ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="department-manage-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-6 col-md-6'>
                <?php  if(isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'].'/once-department-create', $this->params['permList'])):?>
                    <?= Html::a("</i>新增一级科室", ['once-department-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-modal-size'=>'normal']) ?>
                <?php endif?>
                <?php  if(isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'].'/second-department-create', $this->params['permList'])):?>
                    <?= Html::a("新增二级科室", ['second-department-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-modal-size'=>'normal']) ?>
                <?php endif?>
            </div>
            <div class = 'col-sm-6 col-md-6'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table  header'],
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
                    'class' => '\kartik\grid\ExpandRowColumn',
                    'defaultHeaderState' => 1,
                    'enableRowClick' => false,
                    'collapseIcon' => '<i class="fa fa-minus btn-box-tool"></i>',
                    'expandIcon' => '<i class="fa fa-plus btn-box-tool"></i>',
                    'detailUrl' => Url::to(['@apiDepartmentManageSpotSecondDepartmrntSubclass']),
                    'value' => function ($model, $key, $index) {
                        return GridView::ROW_COLLAPSED;//配置默认展开或是收缩
                    }
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'attribute' => 'id',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'attribute' =>  'name',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return '--';
                    }
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
                    'attribute' => 'room_type',
                    'value' => function ($model) {
                        return '--';
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{update}',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/once-department-update', $this->params['permList'])) {
                                return false;
                            }
                            $options = array_merge([
                                'data-pjax' => 0,
                                'role'=>'modal-remote',
                                'data-modal-size'=>'middle'
                            ]);
                            return Html::a('修改', Url::to(['@spotDepartmentManageOnceDepartmentUpdate', 'id' => $key]),$options);
                        },
                    ],
                ],
            ],
            'striped' => false,
            'condensed' => false,
            'hover' => true,
            'bordered' => false,
        ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
