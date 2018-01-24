<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\starroom\models\search\AdimageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$baseUrl = Yii::$app->request->baseUrl;
$this->title = '行为日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>

    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-12 col-md-12'>
                <?= $this->render('_search', ['model' => $searchModel, 'spotList' => $spotList, 'moduleList' => $moduleList, 'actionList' => $actionList]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页

                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'username',
                ],
//                 [
//                     'label' => 'IP地址',
//                     'attribute' => 'ip',

//                 ],
                [
                    'attribute' => 'spot_name',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'module',
//                     'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'value' => function ($data) use($moduleList) {
                        return $moduleList[$data->module] . '(' . $data->module . ')';
                    },
                ],
                [
                    'label' => '动作',
                    'attribute' => 'action',
                    'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                    'value' => function ($data) use($actionList) {
                        return $actionList[$data->action] . '(' . $data->action . ')';
                    },
                ],
                [
                    'attribute' => 'data',
                    'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                ],
                'operation_time:datetime',
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{view}{delete}',
                    'headerOptions' => ['style' => 'width:100px']
                ],
            ],
        ]);
        ?>
    </div>
    <?php Pjax::end();?>
</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
