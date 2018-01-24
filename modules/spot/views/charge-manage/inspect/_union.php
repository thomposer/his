<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css');
?>
<div class="inspect-item-union row">
    <div class="table-responsive">
        <?php $form = ActiveForm::begin(['action' => Url::to(['@spotChargeManageInspectUnion', 'id' => $inspect_id]), 'id' => 'cure-record']); ?>
        <input type='hidden' name='inspect_id'  value='<?= $inspect_id ?>'>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover inspect_union'],
            'layout' => '{items}',
            /* 'filterModel' => $searchModel, */
            'columns' => [
                [
                    'attribute' => 'id',
                    'contentOptions' => ['class' => 'items-select-id'],
                    'headerOptions' => ['class' => 'col-md-1'],
                ],
                [
                    'attribute' => 'item_name',
                    'format' => 'raw',
                    'value' => function ($model)use($newRecord) {
                        if ($model->id) {
                            $val = Html::encode($model->item_name);
                        } else {
                            $val = $model->item_name;
                        }
                        $newRecordVal=$newRecord==1?1:2;
                        $html = $val . "<input type='hidden' name='item_id[]' class='checkitemid' value='$model->id'>".
                                "<input type='hidden' name='deleted[]' class='deleted' value=2>".
                                "<input type='hidden' name='newRecord[]' class='new-record' value=$newRecordVal>".
                                "<input type='hidden' name='unionId[]' value='$model->unionId'>";
                        return $html;
                    },
                    'contentOptions' => ['class' => 'select-items'],
                    'headerOptions' => ['class' => 'col-md-3'],
                ],
                [
                    'attribute' => 'english_name',
                    'contentOptions' => ['class' => 'item-english_name'],
                    'headerOptions' => ['class' => 'col-md-2'],
                ],
                [
                    'attribute' => 'unit',
                    'contentOptions' => ['class' => 'item-unit'],
                    'headerOptions' => ['class' => 'col-md-1'],
                ],
                [
                    'attribute' => 'reference',
                    'contentOptions' => ['class' => 'item-ref'],
                    'headerOptions' => ['class' => 'col-md-2'],
                ],
                [
                    'attribute' => '',
                    'format' => 'raw',
                    'value' => function ($model)use($dataProvider) {
                        if (count($dataProvider->models) > 1) {
                            $operation = " <div class = 'form-group'>
                                            <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-delete'>
                                                <i class = 'fa fa-minus'></i>
                                            </a>
                                             <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-add'>
                                                <i class = 'fa fa-plus'></i>
                                            </a>
                                        </div>";
                        } else {
                            $operation = " <div class = 'form-group'>
                                           <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-delete' style='display:none;'>
                                                <i class = 'fa fa-minus'></i>
                                            </a>
                                            <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-add'>
                                                <i class = 'fa fa-plus'></i>
                                            </a>
                                            </div>";
                        }
                        return $operation;
                    },
                    'headerOptions' => ['class' => 'col-md-3'],
                ],
            ],
        ]);
        ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

$this->registerJs("
        $('.select2').select2();
        var len = $('#cure-record').find('.inspect_union').find('tr').length-1;
        if (len >= 2) {
            $(\".clinic-delete\").show();
            $(\".clinic-add\").hide();
            $('.clinic-add').last().show();
        }else {
            $(\".clinic-delete\").first().hide();
        }
")?>



