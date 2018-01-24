<?php

use yii\helpers\Html;
use dosamigos\datetimepicker\DateTimePicker;

?>
<?php $this->beginBlock('renderCss') ?>
<?php
    $css = <<<CSS
    .datetimepicker table tr td.active.active {
        color: #55657d;
        background-color: #E5F2FF;!important;
        background: #E5F2FF;!important;
    }
    .datetimepicker table tr td.day:hover {
        color: #ffffff;!important;
        background: #76A6EF;!important;
        cursor: pointer;!important;
    }      
CSS;
    $this->registerCss($css);
?>
<?php $this->endBlock() ?>
<div style="float: right">
    <span class='search-default'>筛选：</span>
    <div style="width: 170px;float: left;margin-right: 5px">
        <?=
        DateTimePicker::widget([
            'id' => 'chartDate',
            'name' => 'chart_date',//当没有设置model时和attribute时必须设置name
            'language' => 'zh-CN',
            'template' => '{input}{button}',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'minView' => 2,
                'pickerPosition' => 'bottom-left',
            ],
            'options' => [
                'placeholder'=>'请选择开始日期',
            ]
        ]);
        ?>
    </div>
    <?= Html::button('搜索', ['class' => 'btn btn-default', 'id' => 'selectChart']) ?>
</div>
<span class="clearfix"></span>
