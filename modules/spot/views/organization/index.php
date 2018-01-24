<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot\models\Spot;
use app\modules\spot\models\Organization;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '机构管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="organization-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
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
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
//         'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'spot_name',
            ],
            [
                'attribute' => 'spot',
            ],
            [
                'attribute' => 'contact_iphone',
            ],
            [
                'attribute' => 'contact_name',
            ],
            [
                'attribute' => 'spot_count',
                'value' => function ($searchModel){
                    return Spot::find()->where(['parent_spot' => $searchModel->id,'status' => 1])->count();
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($searchModel){
                    if($searchModel->status == 2){
                        return Html::tag('span',Organization::$getStatus[$searchModel->status],['class' => 'red']);
                    }
                    return Organization::$getStatus[$searchModel->status];
                }
            ],
            'create_time:datetime',

            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{view}{update}{delete}{sawSpot}',
                'headerOptions' => [ 'class' => 'col-xs-2 col-sm-2 col-md-2'],

                'buttons' => [
                    
                    'sawSpot' => function ($url,$model,$key){
                        
                        if(!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@spotIndexIndex'), $this->params['permList'])){
                            return false;
                        }
                        /*查看诊所*/
                        return Html::a('<span class="icon_button_view glyphicon glyphicon-home" title="查看诊所"  data-toggle="tooltip"></span>',['@spotIndexIndex','parent_spot' => $key],[ 'data-pjax' => '0']);
                    }
                ],
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
