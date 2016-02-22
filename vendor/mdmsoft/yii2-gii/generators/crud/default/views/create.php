<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= "<?php "?> AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?= "<?php "?> $this->beginBlock('renderCss')?>

<?= "<?php "?> $this->endBlock();?>
<?= "<?php "?> $this->beginBlock('content')?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create col-xs-12">
    <div class = "box">
        <div class = "box-body">    

            <h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
        
            <?= "<?= " ?>$this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
<?= "<?php "?> $this->endBlock()?>
<?= "<?php "?> $this->beginBlock('renderJs')?>

<?= "<?php "?> $this->endBlock()?>
<?= "<?php "?> AutoLayout::end()?>