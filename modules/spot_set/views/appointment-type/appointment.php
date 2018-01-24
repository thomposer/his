<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\charge\models\search\SearchCharge */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约设置';
// $this->params['breadcrumbs'][] = ['label' => '诊所设置', 'url' => ['index']];
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
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock() ?>

<?php $this->beginBlock('content'); ?>
<div class="charge-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>    
        <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
        <?= $this->render('_timeConfig', ['model' => $model]); ?>
    <?php Pjax::end(); ?>   
</div>
<?php $this->endBlock(); ?>
<?php  $this->beginBlock('renderJs')?>
<script type="text/javascript">
		require(["<?= $baseUrl ?>"+"/public/js/spot/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		})
</script>
<?php  $this->endBlock()?>
<?php AutoLayout::end(); ?>
