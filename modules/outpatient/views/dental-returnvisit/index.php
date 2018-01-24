<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\outpatient\models\DentalFirstTemplate;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\outpatient\models\search\DentalFirstTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '口腔复诊病历模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="dental-first-template-index col-xs-10">

   <div class = "box">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/dentalreturnvisit-create', $this->params['permList'])):?>
           <?= Html::a("<i class='fa fa-plus'></i>新增", ['dentalreturnvisit-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
           <?php endif?>
        </div>
      </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout'=> '{items}<div class="text-right">{summary}{pager}</div>',
        'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'hideOnSinglePage' => false,//在只有一页时也显示分页
            'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
            'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
            'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
            'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
        ],
        'columns' => [

            'name',
            [
                'attribute' => 'type',
                'value' => function($searchModel){
                    return DentalFirstTemplate::$getType[$searchModel->type];
                }
            ],
            'create_time:datetime',
            'username',
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{dentalreturnvisit-view}{dentalreturnvisit-update}{dentalreturnvisit-delete}',
                'buttons' => [
                    
                    'dentalreturnvisit-view' => function ($url, $model, $key){
                       
                        if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/dentalreturnvisit-view', $this->params['permList'])){
                            return false;
                        }
                        
                        $options = [
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                            'class' => 'op-group-a',
                        ];
                        /* fa-eye是查看 */
                        return Html::a('查看', $url, $options);
                    },
                    'dentalreturnvisit-update' => function ($url, $model, $key){
                    
                        if((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/dentalreturnvisit-update', $this->params['permList'])) || $model->type == 1){
                            return false;
                        }
                        
                        $options = [
                            'aria-label' => Yii::t('yii', '修改'),
                            'data-pjax' => '0',
                            'class' => 'op-group-a',
                        ];
                        /* fa-eye是查看 */
                        return Html::a('修改', $url, $options);
                    },
                    'dentalreturnvisit-delete' => function ($url, $model, $key){
                    
                        if((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/dentalreturnvisit-delete', $this->params['permList'])) || $model->type == 1 ){
                            return false;
                        }
                        
                        $options = [
                            'aria-label' => Yii::t('yii', '删除'),
                            'data-pjax' => '1',
                            'class' => 'op-group-a',
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => false,
                            'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        ];
                        /* fa-eye是查看 */
                        return Html::a('删除', $url, $options);
                    }
                        
                ]
            ],
        ],
    ]); ?>
    </div>
    
</div>
<?php  Pjax::end()?>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
