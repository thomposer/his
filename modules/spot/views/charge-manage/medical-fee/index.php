<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\MedicalFeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊金管理';
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
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/medical-fee-create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spotChargeManageMedicalFeeCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
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
                'template' => '{medical-fee-view}{medical-fee-update}{medical-fee-update-status}',
                'buttons' => [
                    'medical-fee-view'=>function($url,$model){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/medical-fee-view', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('查看', $url, ['class'=>'op-group-a']);
                    },
                    'medical-fee-update'=>function($url,$model){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/medical-fee-update', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('修改', $url, ['class'=>'op-group-a']);
                    },
                    'medical-fee-update-status' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/medical-fee-update-status', $this->params['permList'])) {
                            return false;
                        }

                        if($model->status == 1){
                            $title = '停用';
                            $deleteColor = false;
                            $message = "确认停用吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认停用后，该诊金在诊所下也会被停用并且医嘱套餐里该诊金也会被删除。</span>";
                        }else{
                            $title = '启用';
                            $deleteColor = true;
                            $message = '确认启用吗？';
                        }

                        $options = [
                            'title' => $title,
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => $deleteColor,
                            'data-confirm-message' => $message,
                            'class'=>'op-group-a'
                        ];
                        return Html::a($options['title'], $url, $options);
                    },
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
