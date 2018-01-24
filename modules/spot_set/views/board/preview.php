<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
CrudAsset::register($this);
$this->title = '公告配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="board-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
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
                
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [

                'id',
                'name',
                [

                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                                'target'=>'blank'
                            ]);
                            $fileUrl = Yii::$app->params['cdnHost'].$model->file_url;
                            return Html::a('查看', $fileUrl, $options);
                        }
                    ]
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
