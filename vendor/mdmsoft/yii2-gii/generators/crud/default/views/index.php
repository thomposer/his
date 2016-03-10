<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<?= "<?php "?>AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?= "<?php "?>$this->beginBlock('renderCss')?>
    <?= "<?php "?>AppAsset::addCss($this, '@web/public/css/search.css')?>
<?= "<?php "?>$this->endBlock()?>
<?= "<?php "?>$this->beginBlock('content');?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index col-xs-12">
   <?=  "<?php "?> if(in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
    <p>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('添加 {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>
   <?= "<?php "?>endif?>
   <div class = "box">
       <div class = "box-body"> 
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " /*. ($generator->indexWidgetType === 'grid' ? "// " : "")*/ ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel'=> <?= $generator->generateString("首页") ?>,
            'prevPageLabel'=> <?= $generator->generateString("上一页") ?>,
            'nextPageLabel'=> <?= $generator->generateString("下一页") ?>,
            'lastPageLabel'=> <?= $generator->generateString("尾页") ?>,
        ],
        <?= !empty($generator->searchModelClass) ? "/*'filterModel' => \$searchModel,*/\n        'columns' => [\n" : "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            [
                'class' => 'app\common\component\ActionColumn'
            ],
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
        </div>
    </div>
</div>
<?= "<?php "?>$this->endBlock();?>
<?= "<?php "?>$this->beginBlock('renderJs');?>

<?= "<?php "?>$this->endBlock();?>
<?= "<?php "?>AutoLayout::end();?>
