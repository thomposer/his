<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= "<?php "?> AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?= "<?php "?> $this->beginBlock('renderCss')?>

<?= "<?php "?> $this->endBlock();?>
<?= "<?php "?> $this->beginBlock('content')?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view col-xs-12">
    <div class = "box">
        <div class = "box-body">  
            <h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
        
            <p>
            <?= "<?php "?>if(in_array($this->params['requestModuleController'].'/update', $this->params['permList'])):?>
                <?= "<?= " ?>Html::a(<?= $generator->generateString('修改') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-success']) ?>
            <?= "<?php "?>endif; ?>
            <?= "<?php "?>if(in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])):?>
                <?= "<?= " ?>Html::a(<?= $generator->generateString('删除') ?>, ['delete', <?= $urlParams ?>], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => <?= $generator->generateString('你确定要删除此项吗?') ?>,
                        'method' => 'post',
                    ],
                ]) ?>
            <?= "<?php "?>endif; ?>
                <?= "<?= " ?>Html::a(<?= $generator->generateString('返回列表') ?>,['index'],['class' => 'btn btn-primary'])?>
            </p>

    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
        ],
    ]) ?>
        </div>
    </div>
</div>
<?= "<?php "?> $this->endBlock()?>
<?= "<?php "?> $this->beginBlock('renderJs')?>

<?= "<?php "?> $this->endBlock()?>
<?= "<?php "?> AutoLayout::end()?>
