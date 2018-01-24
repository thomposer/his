<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot_set\models\Room;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\SpotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待整理诊室';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="spot-index col-xs-12">
   <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
   <div class = "box">
   <div class = 'row search-margin'>
   </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            [
                'attribute' => 'clinic_name',
                'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3']
            ],
            [
                'attribute' => 'treatment_time',
                'value'=>  function ($model){
                    return date('Y-m-d H:i',$model->treatment_time);
                },
                'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3']
            ],
            [
                'attribute' => 'waite_time',
                'value'=>  function ($model){
                    return $model::timediff($model->treatment_time,time());
                },
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
            ],
            [
                'attribute' => 'clean_status',
                'value'=>  function ($model){
                    return Room::$getCleanStatus[$model->clean_status];
                },
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
            ],
            [
                'class' => 'app\common\component\ActionColumn',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                'template' => '{finish}',
                'buttons' => [
                    
                    'finish' => function ($url,$model,$key){
                        if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/finish', $this->params['permList'])){
                            return false;
                        }
                        $options = [
                            'class' => 'icon_button_view glyphicon glyphicon-ok',
                            'data-confirm'=>false,
                            'data-method'=>false,
                            'data-request-method'=>'post',
                            'role'=>'modal-remote',
                            'data-toggle'=>'tooltip',
                            'data-confirm-title'=>'系统提示',
                            'data-confirm-message'=> '是否确认整理完成？',
                            'data-delete'=> false,
                            'title'=>'整理完成'
                        ];
                        return Html::a('',Url::to(['@roomIndexFinish', 'id' => $model->id]),$options);
                    }
                ]
                
            ],
        ],
    ]); ?>
    </div>
    <?php Pjax::end();?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
