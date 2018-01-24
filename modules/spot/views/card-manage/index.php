<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\spot\models\CardManage;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CardManageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡中心';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '充值卡配置', 'url' => Url::to(['@spotCardManageGroupIndex'])],
        ['title' => '套餐卡配置', 'url' => Url::to(['@spotCardManagePackageCardIndex'])],
        ['title' => '服务卡管理', 'url' => Url::to(['@spotCardManageIndex'])],
    ],
];

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="card-manage-index col-xs-12">
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box delete_gap">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
           <?php //echo Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
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
           // 'f_physical_id',
            'f_card_id',
            'f_card_type_code',
            [
               'attribute'=>'cardName',
                'value'=>function($model){
                    return isset(CardManage::$cardTypeCode[$model->f_card_type_code])?CardManage::$cardTypeCode[$model->f_card_type_code]:'';
                }
            ],
            'f_identifying_code',
            [
                'attribute'=>'f_status',
                'value'=>function($model){
                    return CardManage::$getStatus[$model->f_status];
                }
            ],
             'f_card_desc',
            [
                'attribute'=>'f_effective_time',
                'value'=>function($model){
                    return CardManage::getDateTime($model->f_effective_time);
                }
            ],
            [
                'attribute'=>'f_activate_time',
                'value'=>function($model){
                    return CardManage::getDateTime($model->f_activate_time);
                }
            ],
            [
                'attribute'=>'f_invalid_time',
                'value'=>function($model){
                    return CardManage::getDateTime($model->f_invalid_time);
                }
            ],
            [
                'attribute'=>'f_create_time',
                'value'=>function($model){
                    return CardManage::getDateTime($model->f_create_time);
                }
            ],
           // 'f_effective_time:datetime',
           // 'f_activate_time:datetime',
           // 'f_invalid_time:datetime',
           // 'f_create_time:datetime',
            [
                'class' => 'app\common\component\ActionTextColumn',
                'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                'template' => '{update}',
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
