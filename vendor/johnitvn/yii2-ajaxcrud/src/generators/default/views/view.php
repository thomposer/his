<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\widgets\DetailView;
use app\common\AutoLayout;
use yii\helpers\Html;
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
?>
<?= "<?php "?>  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?= "<?php "?> $this->beginBlock('renderCss')?>

<?= "<?php "?>  $this->endBlock();?>
<?= "<?php "?>  $this->beginBlock('content')?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?= "<?= "?>  Html::encode($this->title) ?></span>
       <?= "<?= "?> Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div> 
     <div class = "box-body">
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
<?= "<?php "?>  $this->endBlock()?>
<?= "<?php "?>  $this->beginBlock('renderJs')?>

<?= "<?php "?>  $this->endBlock()?>
<?= "<?php "?>  AutoLayout::end()?>