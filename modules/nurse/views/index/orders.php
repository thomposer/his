<?php

use app\assets\AppAsset;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\outpatient\models\CureRecord;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>
<?php $this->beginBlock('renderCss')?>
<?php
$css = <<<CSS
    #ajaxCrudModal .modal-body {
         padding: 15px 15px;
    }
    #ajaxCrudModal .modal-header {
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
    }
   .table .op-group a{
        margin-right: 0; 
   }
        
CSS;
$this->registerCss($css);



?>
<?php $this->endBlock()?>
<?php Pjax::begin(['id' => 'orders-detail-'.$param, 'enablePushState' => false]); ?>
<div class = 'row row-search-margin'>
</div>
<div class="orders-data-form">
    <div class = 'cost-bg'>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover  add-table-border'],
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
                        'attribute' => 'name',
                        'label' => '医嘱项目',
                        'value' => function ($dataProvider) {
                            return $dataProvider['name'];
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                        'format' => 'raw',
                        'label' => '状态',
                        'value' => function($dataProvider) {
                                switch ($dataProvider['type']){
                                    case 1:
                                        return \app\modules\outpatient\models\InspectRecord::getExecuteStatusOptions($dataProvider['status'],1);
                                    break;
                                    case 2:
                                        return \app\modules\outpatient\models\CheckRecord::getExecuteStatusOptions($dataProvider['status'],1);
                                    break;
                                    case 3:
                                        return \app\modules\outpatient\models\CureRecord::getExecuteStatusOptions($dataProvider['status'],1);
                                    break;
                                    case 4:
                                        return \app\modules\outpatient\models\RecipeRecord::getStatusOptions($dataProvider['status'],1);
                                    break;
                                }
                        }
                    ],
                    [
                        'class' => 'app\common\component\ActionColumn',
                        'template' => '{apply}{report}{recipe}{cure}',
                        'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                        'buttons' => [
                            'apply' => function($url, $model, $key) {
                                if($model['type']!=1){
                                    return false;
                                }
                                $options = [
                                   'data-pjax' => '0',
                                   'class'=>'op-group-a btn-inspect-application-print',
                                   'record_id'=>$model['record_id'],
                                   'id'=>$model['id'],
                                ];
                                return Html::a('申请单','#', $options) . '<span style="color:#99a3b1">丨</span>';
                            },
                            'report' => function($url, $model, $key) {
                                if($model['type']!=1){
                                    return false;
                                }
                                $options = [
                                    'data-pjax' => '0',
                                     'record_id'=>$model['record_id'],
                                     'id'=>$model['id'],
                                ];
                                if($model['status']==1){
                                    $options['class']= 'op-group-a btn-inspect-report-print';
                                }else{
                                     $options['class']= 'op-group-a-disable ';
                                }
                                return Html::a('报告', '#', $options);
                            },
                            'cure' => function($url, $model, $key) {
                                if($model['type']!=3){
                                    return false;
                                }
                                $options = [
                                    'data-pjax' => '0',
                                    'record_id'=>$model['record_id'],
                                    'id'=>$model['id'],
                                    'class' => 'op-group-a btn-nurse-cure-print'
                                ];
                                return Html::a('治疗单', '#', $options);
                            },
                            'recipe' => function($url, $model, $key) {
                                if($model['type']!=4){
                                    return false;
                                }
                                $recipeType = '';
                                if($model['drug_type'] == '20'){
                                    $recipeType = '精二处方';
                                    $filterType = 1;
                                }else{
                                    $recipeType = '儿科处方';
                                    $filterType = 2;
                                }
                                $options = [
                                    'data-pjax' => '0',
                                    'record_id'=>$model['record_id'],
                                    'id'=>$model['id'],
                                    'class' => 'op-group-a btn-nurse-recipe-print',
                                    'data-type' => $filterType,
                                ];
                                
                                return Html::a($recipeType, '#', $options);
                            },
                        ]
                   ]
                        
                        
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
