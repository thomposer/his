<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot_set\models\Room;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\Search\RoomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊室管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$publicSettingPng = $baseUrl . '/public/img/'. '/tab/tab_setting.png';
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="room-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>    
   <div class = "box">
    <div class = 'row search-margin'>
      <div class = 'col-sm-4 col-md-4'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
       <?php endif?>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/doctor-room-config', $this->params['permList'])):?>
       <?= Html::a(Html::tag("i","",['class' => 'icon_url','style' => 'background:url('. $publicSettingPng . ');background-size:16px;'])."医生常用诊室设置", ['doctor-room-config'], ['class' => 'font-body2','data-pjax' => 0, 'style' => 'text-decoration:underline;font-size:14px;','target' => '_blank']) ?>
       <?php endif?>
    </div>
    <div class = 'col-sm-8 col-md-8'>
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
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'id',
               'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'clinic_name',
            ],
            'floor',
            [
                'attribute'=>'clinic_type',
                'value'=>function($searchModel){
                    return Room::$getClinicType[$searchModel->clinic_type];
                }
            ],
            [
                'attribute'=>'status',
                'format' => 'raw',
                'value'=>function($searchModel){
                    if($searchModel->status == 3){
                        return Html::tag('span','已删除',['class' => 'red']);
                    }
                    return Room::$getStatus[$searchModel->status];
                }
            ],
            // 'spot_id',
            // 'create_time',
            // 'update_time:datetime',

            [
                'class' => 'app\common\component\ActionTextColumn',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
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
