<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use app\modules\spot\models\PackageCardService;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\PackageCardServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '套餐卡服务类型管理';
$this->params['breadcrumbs'][] = ['label' => '卡中心', 'url' => ['index']];
$this->params['breadcrumbs'][] = '套餐卡配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
CrudAsset::register($this);

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/packageCard.css') ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
   <div class="package-card-service-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div id="ajaxCrudDatatable" class = 'box'>
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['package-card-index']), ['class' => 'right-cancel', 'data-pjax' => 0]) ?>      
        </div>
        <div class = 'row search-margin'>
          <div class = 'col-sm-2 col-md-2'>
                <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/package-card-service-create', $this->params['permList'])):?>
                <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spotCardManagePackageCardServiceCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-toggle'=>'tooltip']) ?>
                <?php endif?>
          </div>
          <div class = 'col-sm-10 col-md-10'>
                             <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
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
                    'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => [
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'name',
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'status',
                    'value' => function($model){
                        return PackageCardService::$getStatus[$model->status];
                    }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{update}{update-status}',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-service-update', $this->params['permList'])) {
                                return Html::a('修改', [Url::to('@spotCardManagePackageCardServiceUpdate'), 'id' => $model->id], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                            } else {
                                return false;
                            }
                        },
                        'update-status' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-service-update-status', $this->params['permList'])) {
                                $options = [
                                        'data-confirm' => false, 
                                        'data-method' => false, 
                                        'data-toggle' => 'tooltip', 
                                        'role' => 'modal-remote', 
                                        'data-confirm-title' => '系统提示', 
                                        'data-pjax' => 0
                                        ];
                                if(1 == $model->status){
                                    $options['data-confirm-message'] = "确认停用吗？";
                                    return Html::a('停用', [Url::to('@spotCardManagePackageCardServiceUpdateStatus'), 'id' => $model->id], $options);
                                }else{
                                    $options['data-confirm-message'] = '确认启用吗？';
                                    return Html::a('启用', [Url::to('@spotCardManagePackageCardServiceUpdateStatus'), 'id' => $model->id], $options);
                                }
                            } else {
                                return false;
                            }
                        },
                            ],
                        ],
                    ],
                'striped' => false,
                'condensed' => false, 
                'hover' => true,
                'bordered' => false,
                    
                ])?>
     </div>
<?php  Pjax::end()?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php  Modal::end(); ?>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
