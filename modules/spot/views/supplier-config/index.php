<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\SupplierConfSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '供应商配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="supplier-conf-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
                <?php endif ?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'id',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model::$getStatus[$model->status];
                    },
                ],
                [
                    'attribute' => 'create_time',
                    'value' => function ($model) {
                        return date('Y-m-d H:i:s', $model->create_time);
                    },
                ],
                // 'update_time',
                [
                    'class' => 'app\common\component\ActionTextColumn'
                ],
            ],
        ]);
        ?>
    </div>
    <?php Pjax::end() ?>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
