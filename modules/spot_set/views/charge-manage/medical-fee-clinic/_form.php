<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\MedicalFee */
/* @var $form yii\widgets\ActiveForm */
?>
<?php Pjax::begin(['id' => 'meidcal-fee-clinic-from_'.$param,'enablePushState' => false])?>
<?php $form = ActiveForm::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($dataProvider)use($feeIdList){
                    if(isset($feeIdList[$dataProvider['id']])){
                        return ['class' => 'check-type','checked' => 'checked'];
                    }else{
                        return ['class' => 'check-type'];
                    }  
                },
                'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                'name' => 'MedicalFeeId[]',
            ],
            [  
                'attribute' => 'remarks',
            ],
            [  
                'attribute' => 'price',
            ],
            [  
                'attribute' => 'note',
            ],
        ],
    ]); ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end();?>
<?php
$this->registerJs("
        $('.select-on-check-all').unbind('click').on('click', function () {
           var value = $(this).prop('checked');
            if (value) {
                $('input:checkbox[name=\"MedicalFeeId[]\"]').prop('checked', true);
            } else {
                $('input:checkbox[name=\"MedicalFeeId[]\"]').prop('checked', false);
            }
        }); 
        //渲染弹窗时判断是否全选
        flag = 0;
        $('input:checkbox[name=\"MedicalFeeId[]\"]').each(function (index) {
                var value = $(this).prop('checked');
                if (!value) {
                    flag++;
                }
        });
        if(!flag){
            $('.select-on-check-all').prop('checked', true);
        }
        //点击选项时是否需要全选
        $('input:checkbox[name=\"MedicalFeeId[]\"]').unbind('click').on('click', function () {
            flag = 0;
            $('input:checkbox[name=\"MedicalFeeId[]\"]').each(function (index) {
                    var value = $(this).prop('checked');
                    if (!value) {
                        flag++;
                        return ;
                    }
            });
            if(!flag){
                $('.select-on-check-all').prop('checked', true);
            }else{
                $('.select-on-check-all').prop('checked', false);
            }
        }); 
")?>
