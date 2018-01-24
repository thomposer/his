<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\MedicalFeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊金配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="medical-fee-index col-xs-10">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
   <div class = "box">
   <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/medical-fee-clinic-create', $this->params['permList'])):?>
       <?= 
           Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spot_setChargeManageMedicalFeeClinicCreate']),
               ['class' => 'btn btn-default font-body2','data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']
               ) 
        ?>
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
//            ['class' => 'yii\grid\SerialColumn'],

            [  
                'attribute' => 'id',
            ],
            [  
                'attribute' => 'remarks',
            ],
            [  
                'attribute' => 'price',
            ],
            [  
                'attribute' => 'note',
            ],
            [  
                'attribute' => 'status',
                'value'=>  function ($model){
                        return $model::$getStatus[$model->status];
                },
            ],
            [  
                'attribute' => 'create_time',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->create_time);
                },
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{medical-fee-clinic-view}{medical-fee-clinic-delete}',
                'buttons' => [
                    'medical-fee-clinic-view' => function($url,$model,$key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/medical-fee-clinic-view', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'data-pjax' => '0',
                            'class' => 'op-group-a'
                        ]);
                        return Html::a('查看', $url, $options);
                    },
                    'medical-fee-clinic-delete' => function ($url, $model, $key) {

                        $message = "确认删除吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认删除后，医嘱套餐里该诊金也会被删除。</span>";
                        $options = [
                          'data-pjax' => 0,
                          'role' => 'modal-remote',
                          'data-request-method' => 'post',
                          'data-confirm-title'=> '系统提示',
                          'data-confirm-message'=> $message
                        ];

                        if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/medical-fee-clinic-delete', $this->params['permList'])){
                            return Html::a('删除', ['medical-fee-clinic-delete', 'id' => $model->id],$options);
                        }
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
    <?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
