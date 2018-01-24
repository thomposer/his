
<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统概况';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset::addCss($this, '@web/public/css/overview/index.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/overview/detail.css'); ?>

<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<div class="overview-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class = 'row'>
        <?= $this->render('_top_bar', ['model'=>$searchModel,'type'=>1,'overviewNum'=>$overviewNum]); ?>
    </div>
    <div id="ajaxCrudDatatable box" class = "box">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover header'],
            'headerRowOptions' => ['class' => 'header'],
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
<script type = "text/javascript">
   	var baseUrl = '<?= $baseUrl ?>';
   	var selectSpotUrl = '<?= Url::to(['@manageSites']); ?>';
	require(['<?= $baseUrl?>'+'/public/js/overview/detail.js?v=22'],function(main){
		main.init();
	})
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?> 