<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search hidden-xs">
    <?= "<?php " ?>$form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 4) {
        echo "    <?= " . $generator->generateActiveSearchField($attribute) . "->textInput(['placeholder' => '请输入'.\$attributeLabels['$attribute'] ]) ?>\n\n";
    } else {
        echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . "->textInput(['placeholder' => '请输入'.\$attributeLabels['$attribute'] ]) ?>\n\n";
    }
}
?>
    <div class="form-group search_button">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('搜索') ?>, ['class' => 'btn btn-default']) ?>
        <?= "<?php // " ?>Html::a(<?= $generator->generateString('重置') ?>,[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>
</div>