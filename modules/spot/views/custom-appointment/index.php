<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use app\modules\spot_set\models\SpotType;
use app\modules\spot\models\OrganizationType;
use kartik\time\TimePickerAsset;
TimePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '服务类型';
// $this->params['breadcrumbs'][] = ['label' => '诊所设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
CrudAsset::register($this);
//$tabData = [
//    'titleData' => [
//        ['title' => '全局时间设置', 'url' => Url::to(['@spot_setAppointmentTimeConfig']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
//        ['title' => '预约服务类型配置', 'url' => Url::to(['@spot_setCustomAppointment']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
//        ['title' => '医生-服务关联配置', 'url' => Url::to(['@spot_setUserAppointmentConfigIndex']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
//    ],
//];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/timeConfig.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
   <div class="spot-type-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class = "box">
<!--   		--><?php //echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
        <div class = 'row search-margin'>
          <div class = 'col-sm-2 col-md-2'>
                <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
                <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-toggle'=>'tooltip']) ?>
                <?php endif?>
          </div>
          <div class = 'col-sm-10 col-md-10'>
          </div>
       </div>
        <?=GridView::widget([
        //                'id'=>'crud-datatable',
        'dataProvider' => $dataProvider,
        //                'filterModel' => $searchModel,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border'],
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
        'striped' => false,
        'bordered' => false,
        'columns' => [
                'name',
                'time',
                [
                    'attribute' => 'record_type',
                    'value' => function($model){
                        return OrganizationType::$getRecordType[$model->record_type];
                    }
                ],
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return OrganizationType::$getStatus[$model->status];
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
        //                            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'template' => '{update}{delete}',
                    'ajaxList' => [
                        'update' => true,
                        'delete' => true
                        ],
                    'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])){
        //                                            if(0 == $model->is_delete){
                                        return Html::a('删除', ['delete', 'id' => $model->id], ['title' => '删除','data-pjax' => 0,
                                            'role' => 'modal-remote','data-request-method' => 'post','data-confirm-title'=>'系统提示','data-confirm-message'=>"确定删除该机构下的服务类型？<br><span style='font-size: 12px;color:#97A3B6;'>确认删除后，对应诊所及医生关联此服务类型也会删除。</span>"]);
        //                                            }
                                }
                            }
                        ]
                ],
        ],
        ])?>
     </div>
<?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">

		require(["<?= $baseUrl ?>"+"/public/js/spot/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		})
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
