<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\spot\models\CheckCode;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\check_code\models\search\checkCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '代码诊断';
$this->params['breadcrumbs'][] = "代码诊断";
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="check-code-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
           <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
           <?php endif?>
        </div>
        <div class = 'col-sm-10 col-md-10'>
                        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                </div>
      </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
        'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'hideOnSinglePage' => false,//在只有一页时也显示分页
            'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
            'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
            'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
            'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'spot_id',
            'name',
            'major_code',
            'add_code',
            // 'help_code',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return CheckCode::$getStatus[$model->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{update}{start}',
                'buttons' => [
                        'update' => function($url,$model,$key){
                            return Html::a('修改',$url,['class'=>'op-group-a','data-pjax' => 0]);
                        },
                        'start' =>function($url,$model,$key){
                            if($model->status == 1){
                                $confirmMessage = "确认停用吗？停用后医生接诊时填写初步诊断将找不到该结果项。";
                                $title = "停用";
                                $updateStatus = 2;
                            }else{
                                $confirmMessage = "确认启用吗？启用后医生接诊时填写初步诊断可以搜索到。";
                                $title = "启用";
                                $updateStatus = 1;
                            }
                            $options = array_merge([
                                'class' => 'op-group-a',
                                'data-confirm'=>false,
                                'data-method'=>false,
                                'data-request-method'=>'post',
                                'role'=>'modal-remote',
                                'data-toggle'=>'tooltip',
                                'data-confirm-title'=>'系统提示',
                                'data-delete' => $model->status==1?false:true,
                                'data-confirm-message'=>$confirmMessage,
                                'data-pjax' => '1',
                            ]);
                            return Html::a($title,Url::to(['@spotCheckCodeUpdateStatus','id'=>$model->id,'status'=>$updateStatus]),$options);
                        }
                ]
            ],
        ],
    ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
