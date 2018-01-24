<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot_set\models\SecondDepartment;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\search\SecondDepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '二级科室';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="second-department-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
   <div class = "box">
    <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
       <?php endif?>
    </div>
    <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel,'onceDepartmentInfo' => $onceDepartmentInfo]); ?>
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
            'id',
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
            ],
            [
                'attribute' => 'parent_name',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
            ],

            [
                'attribute' => 'appointment_status',
                'value' => function ($searchModel){
                    return SecondDepartment::$getAppointmentStatus[$searchModel->appointment_status];
                }
            ],
            [
                'attribute' => 'room_type',
                'value' => function ($searchModel){
                    return SecondDepartment::$getRoomType[$searchModel->room_type];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($searchModel){
                    return SecondDepartment::$getStatus[$searchModel->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{update}{delete}',
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
