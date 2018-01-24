<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\medicine\models\search\MedicineDescriptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用药指南';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
CrudAsset::register($this);

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
 <div class="medicine-description-index col-xs-12">
    <div id="ajaxCrudDatatable" class = 'box'>
        <div class = 'row search-margin'>
          <div class = 'col-sm-2 col-md-2'>
                <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
                <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-toggle'=>'tooltip','data-modal-size' => 'large']) ?>
                <?php endif?>
          </div>
          <div class = 'col-sm-10 col-md-10'>
               <?php echo $this->render('_search', ['model' => $searchModel]); ?>
          </div>
       </div>
            <?=GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'options' => ['class' => 'grid-view table-responsive add-table-padding'],
                'tableOptions' => ['class' => 'table-border header'],
                'layout'=> '{items}<div class="text-right">{pager}</div>',
                'pager'=>[
                    //'options'=>['class'=>'hidden']//关闭自带分页
                    
                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => require(__DIR__.'/_columns.php'),         
                'striped' => false,
                'condensed' => false, 
                'hover' => true,
                'bordered' => false,
                    
                ])?>
     </div>
<?php  Pjax::end()?>
</div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
