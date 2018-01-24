<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$actionParams = $generator->generateActionParams();

echo "<?php\n";

?>
use yii\helpers\Url;

return [
    // [
         // 'class' => 'kartik\grid\CheckboxColumn',
         // 'width' => '20px',
    // ],
    // [
         // 'class' => 'kartik\grid\SerialColumn',
         // 'width' => '30px',
    // ],
    <?php
    $count = 0;
    foreach ($generator->getColumnNames() as $name) {   
        if ($name=='id'||$name=='create_time'||$name=='update_time'){
            echo "    // [\n";
            echo "        // 'class'=>'\kartik\grid\DataColumn',\n";
            echo "        // 'attribute'=>'" . $name . "',\n";
            echo "    // ],\n";
        } else if (++$count < 6) {
            echo "    [\n";
            echo "        'class'=>'\kartik\grid\DataColumn',\n";
            echo "        'attribute'=>'" . $name . "',\n";
            echo "    ],\n";
        } else {
            echo "    // [\n";
            echo "        // 'class'=>'\kartik\grid\DataColumn',\n";
            echo "        // 'attribute'=>'" . $name . "',\n";
            echo "    // ],\n";
        }
    }
    ?>
    [
        'class' => 'app\common\component\ActionColumn',
        'ajaxList' => [
            'update' => true, //默认开启ajax的update,delete,关闭view
            'delete' => true
        ],
    ],

];   