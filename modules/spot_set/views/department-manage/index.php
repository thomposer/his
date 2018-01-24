<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
CrudAsset::register($this);

$this->title = '科室配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset::addCss($this, '@web/public/css/overview/detail.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot_set/departmentManage.css') ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="department-manage-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-12 col-md-12'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <div class = "box-body">
            <?php
            $form = ActiveForm::begin([
                'options' => [
                    'class' => 'form-horizontal',
                    'method' => 'post',
                ],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>"
                ]
            ]);
            ?>
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
                        'defaultHeaderState' => 0,
                        'enableRowClick' => false,
                        'collapseIcon' => '<i class="fa fa-minus btn-box-tool"></i>',
                        'expandIcon' => '<i class="fa fa-plus btn-box-tool"></i>',
                        'detailUrl' => Url::to(['@apiDepartmentManageSpotsetSecondDepartmrntSubclass']),
                        'value' => function ($model, $key, $index) {
                            return GridView::ROW_EXPANDED;//配置默认展开或是收缩
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
                ],
                'striped' => false,
                'condensed' => false,
                'hover' => true,
                'bordered' => false,
            ]); ?>
            <?php
                $result = $dataProvider->getModels();
            ?>
            <?php  if(count($result) > 0):?>
                <div class="form-group" style="margin-left: 20px;margin-bottom: 0px;">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                </div>
            <?php endif?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
