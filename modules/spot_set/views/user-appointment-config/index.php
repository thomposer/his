<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\user\models\UserSpot;
use yii\helpers\Url;
use kartik\time\TimePickerAsset;
TimePickerAsset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\search\UserAppointmentConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约设置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '全局时间设置', 'url' => Url::to(['@spot_setAppointmentTimeConfig']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
        ['title' => '预约服务类型配置', 'url' => Url::to(['@spot_setCustomAppointment']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
        ['title' => '医生-服务-诊金关联配置', 'url' => Url::to(['@spot_setUserAppointmentConfigIndex']),'icon_img' => $public_img_path . '/tab/tab_setting.png'],
    ],
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
	<?php AppAsset::addCss($this, '@web/public/css/spot/spotConfig.css')?>
	<?php AppAsset::addCss($this, '@web/public/css/spot/timeConfig.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="user-appointment-config-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box">
   		<?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
       <div class = 'row search-margin'>
 
        <div class = 'col-sm-12 col-md-12'>
             <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
      </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
        'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
        'pager'=>[
            'hideOnSinglePage' => false,//在只有一页时也显示分页
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        'columns' => [

            'userName',
            'departmentName',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return UserSpot::$getStatus[$model->status];
                }
            ],
            [
                'attribute' => 'appointmentTypeName',
                'value' => function($model)use($typeInfo){
                    return Html::encode($typeInfo[$model->id]['type']);
                }
            ],

            [
                'class' => 'app\common\component\ActionTextColumn',
                'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                'template' => '{update}'
            ],
        ],
    ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">
                var apiTypeConfigGetTypeTime = '<?=  Url::to(['@apiTypeConfigGetTypeTime']) ?>';
		require(["<?= $baseUrl ?>"+"/public/js/spot/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		})
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
