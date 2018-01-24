<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css');
AppAsset::addCss($this, '@web/public/css/charge/material.css');

if (!empty($list)) {
    foreach($list as $key=>&$value){
        $value['meta']&&$value['name']=$value['name'].'-'.$value['meta'];
        $value['name']=$value['name'].' (';
        $value['specification']&&$value['name']=$value['name'].$value['specification'];
        $value['price']&&$value['name']=$value['name'].','.$value['price'].'元';
        $value['unit'] && $value['name'] = $value['name'] . '/'.$value['unit'];
        $value['name']=$value['name'].' )';
    }
}
$buttonClass = 'form-group';
if ($update) {
    $buttonClass = 'text-center';
}
?>
<div class = 'padding-width' <?= $update ? '' : 'style = "padding : 0 15px;"' ?> >
    <div class = "row">
        <div class = 'col-md-12'>新增其他收费项</div>
        <div class = 'col-md-12'>
            <?= $form->field($model->getModel('materialModel'), 'id')->dropDownList(ArrayHelper::map($list, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%'])->label(false) ?>
        </div>
    </div>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive', 'id' => 'createMaterial'],
        'tableOptions' => ['class' => 'table table-hover table-border'],
        'headerRowOptions' => ['class' => 'header'],
        'layout' => '{items}',
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-3'],
                'value' => function($dataProvider)use($update) {

                    if ($update) {
                        $showValue = '';
                        if ($dataProvider->manufactor != '') {
                            $showValue .= '生产商：' . Html::encode(Html::encode($dataProvider->manufactor)) . '<br/>';
                        }
                        $showValue .= '零售价：' . $dataProvider->price . '元';
                        return Html::tag('span', Html::encode($dataProvider->name), ['data-toggle' => 'tooltip', 'data-html' => 1, 'data-placement' => 'bottom', 'data-original-title' => $showValue]);
                    }
                }
            ],
            [
                'attribute' => 'specification',
                'headerOptions' => ['class' => 'col-md-1']
            ],
            [
                'attribute' => 'price',
                'headerOptions' => ['class' => 'col-md-1'],
                'headerOptions' => ['style' => 'width : 100px;'],
            ],
            [
                'attribute' => 'num',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width : 100px;'],
                'value' => function($dataProvider)use($update) {
                    if ($update) {
                        return Html::input('number', 'MaterialCharge[num][]', $dataProvider->num, ['class' => 'form-control']);
                    }
                }
            ],
            [
                'attribute' => 'unit',
                'headerOptions' => ['class' => 'col-md-2']
            ],
            [
                'attribute' => 'remark',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
                'value' => function($dataProvider)use($update) {
                    if ($update) {
                        return Html::input('text', 'MaterialCharge[remark][]', $dataProvider->remark, ['class' => 'form-control']);
                    }
                }
            ],
            [
                'attribute' => 'stockNum',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                'value' => function($dataProvider)use($materialTotal) {
                    if($dataProvider->attribute == 2){
                        return $materialTotal[$dataProvider->stockId];
                    }
                   return '--';
                }
            ],
            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1 action-column'],
                'contentOptions' => ['class' => "op-group", 'style' => 'display:table-cell'],
                'buttons' => [
                    'delete' => function($url, $model, $key) {
                        $html = '';
                        $html .= Html::hiddenInput('MaterialCharge[stockId][]', $model->stockId);
                        $html .= Html::hiddenInput('MaterialCharge[chargeInfoId][]', $model->id);
                        $html .= Html::hiddenInput('MaterialCharge[deleted][]') . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
                        return $html;
                    }
                ]
            ],
        ],
    ]);
    ?> 
    <div class="<?= $buttonClass ?>">
        <?php
        if (!$update) {
            echo Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form second-cancel']);
        } else {
            echo Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]);
        }
        ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form', 'id' => 'chargeForm', 'data-url' => Url::to(['confirm-charge']), 'contentType' => 'application/x-www-form-urlencoded', 'data-request-method' => 'post', 'processData' => 1, 'actionUrl' =>  Url::to(['create-material'])]) ?>
    </div>
</div>