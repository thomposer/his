<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="tag-orders">
        <label class="control-label">关联项目</label>        
</div>

<?php Pjax::begin(['id' => 'orders-list-pjax']);?>
<?=

GridView::widget([
    'id' => 'orders-list',
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border header'],
    'layout' => '{items}<div class="text-right">{pager}</div>',
    'pager' => [
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ],
    'columns' => [
        [
            'attribute' => 'name',
            'label' => '医嘱项目',
            'value' => function ($dataProvider) {
                return $dataProvider['name'];
            },
        ],
        [
            'attribute' => 'ordersName',
            'label' => '所属类别',
            'value' => function ($dataProvider) {
                return $dataProvider['ordersName'];
            },
        ],
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => '{deleteUnion}',
            'ajaxList' => [
                'deleteUnion' => true
            ],
            'headerOptions' => [ 'class' => 'col-sm-1 col-md-1' ],
            'buttons' => [
                'deleteUnion' => function ($url, $dataProvider, $key) use($model) {
                    if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/delete-union', $this->params['permList'])) {
                        return false;
                    }
                    $options = [
                        'class' => 'operation',
                        'orders_id' => $dataProvider['id'],
                        'type' => $dataProvider['type'],
                        'role' => 'modal-remote',
                        'title' => '解除关联',
                        'data-toggle' => 'tooltip',
                        'data-confirm-title' => '系统提示',
                        'data-confirm-message' => '确定要解除与“' . Html::encode($dataProvider['name']) . '”的关联吗？',
                        'data-pjax' => '1',
                        'data-request-method' => 'post',
                    ];
                    return Html::a('解除关联', Url::to(['@spotOrdersListDelete', 'tagId' => $model->id, 'id' => $dataProvider['id'], 'type' => $dataProvider['type']]), $options);
                },
                    ]
                ],
            ],
            'striped' => false,
            'condensed' => false,
            'hover' => true,
            'bordered' => false,
        ])?>
<?php Pjax::end();?>